<?php

declare(strict_types=1);

namespace App\Command;

use App\Constant\BatchConstant;
use App\DTO\ParseCityDTO;
use App\DTO\ParseUserDTO;
use App\Entity\City;
use App\Entity\User;
use App\Factory\ReadLinesServiceFactory;
use App\Factory\TableFactory;
use App\Service\CityReaderService;
use Doctrine\ORM\EntityManagerInterface;
use function iter\chunk;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Safe\Exceptions\FilesystemException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * The "FileParseCommand" class.
 */
class FileParseCommand extends Command
{
    use LockableTrait;
    protected static $defaultName = 'file:parse';
    /**
     * @var ReadLinesServiceFactory
     */
    private ReadLinesServiceFactory $readLinesServiceFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;
    /**
     * @var CityReaderService
     */
    private CityReaderService $cityReader;
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var TableFactory
     */
    private TableFactory $tableFactory;
    /**
     * @var CacheItemPoolInterface
     */
    private CacheItemPoolInterface $fileStorage;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    public function __construct(
        ReadLinesServiceFactory $readLinesServiceFactory,
        LoggerInterface $logger,
        CityReaderService $cityReader,
        EntityManagerInterface $entityManager,
        TableFactory $tableFactory,
        CacheItemPoolInterface $fileStorage,
        SerializerInterface $serializer
    ) {
        $this->readLinesServiceFactory = $readLinesServiceFactory;
        $this->logger = $logger;
        $this->cityReader = $cityReader;
        $this->entityManager = $entityManager;
        $this->tableFactory = $tableFactory;
        $this->fileStorage = $fileStorage;
        $this->serializer = $serializer;
        parent::__construct(static::$defaultName);
    }

    protected function configure()
    {
        $this
            ->addOption('filepath', null, InputOption::VALUE_OPTIONAL, 'Путь к файлу')
            ->setDescription('Сохранить данные из файла')
            ->setHelp(
                <<<EOF
Команда <info>%command.name%</info> парсит данные из файла и сохраняет их в файлы и БД.
Пример:
<info>php %command.full_name% --filepath="/tmp/data"</info>
EOF
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filepath = (string) $input->getOption('filepath');
        try {
            $readLinesService = $this->readLinesServiceFactory->create($filepath);
        } catch (FilesystemException $e) {
            $this->logger->error($e->getMessage());

            return 1;
        }

        $iterator = chunk($this->cityReader->cities($readLinesService), BatchConstant::SIZE);
        foreach ($iterator as $cities) {
            $this->saveToFile($cities);
            $this->saveToDB($cities);
        }

        $countUsers = $this->entityManager->getRepository(City::class)->countUsers();
        $table = $this->tableFactory->create($output, array_keys($countUsers[0]), $countUsers);
        $table->render();

        return 0;
    }

    /**
     * @param array|ParseCityDTO $cities
     */
    public function saveToFile(array $cities)
    {
        foreach ($cities as $city) {
            $document = $this->fileStorage->getItem($city->getId());
            if (!$document->isHit()) {
                $value = $this->serializer->serialize($city, JsonEncoder::FORMAT);
                $document->set($value);
                $this->fileStorage->save($document);
            } else {
                $newUsers = [];
                foreach ($city->getUsers() as $newUser) {
                    $newUsers[$newUser->getId()] = [
                        'name' => $newUser->getName(),
                        'phone' => $newUser->getPhone(),
                    ];
                }

                $oldUsers = [];
                $savedCity = $this->serializer->deserialize($document->get(), ParseCityDTO::class, JsonEncoder::FORMAT);
                $savedCityUsers = $savedCity->getUsers();
                foreach ($savedCityUsers as $oldUser) {
                    $oldUsers[$oldUser->getId()] = null;
                }

                $diffUsers = array_diff_key($newUsers, $oldUsers);
                if (!empty($diffUsers)) {
                    $newUsersDto = $savedCityUsers->toArray();
                    foreach ($diffUsers as $diffUserId => $diffUser) {
                        $newUsersDto[] = new ParseUserDTO((string) $diffUserId, $diffUser['name'], $diffUser['phone']);
                    }

                    $newCity = new ParseCityDTO(
                        $savedCity->getId(), $savedCity->getName(), $savedCity->getCountry(), $newUsersDto
                    );
                    $value = $this->serializer->serialize($newCity, JsonEncoder::FORMAT);
                    $document->set($value);
                    $this->fileStorage->save($document);
                }
            }
        }
    }

    /**
     * @param array|ParseCityDTO $cities
     */
    private function saveToDB(array $cities)
    {
        $newCityIds = [];
        $newCities = [];
        $newUsers = [];
        foreach ($cities as $city) {
            $id = $city->getId();
            $newCityIds[] = $id;
            $newCities[$id] = [
                'name' => $city->getName(),
                'country' => $city->getCountry(),
            ];
            foreach ($city->getUsers() as $user) {
                $userId = $user->getId();
                $newUsers[$userId] = [
                    'id' => $userId,
                    'name' => $user->getName(),
                    'phone' => $user->getPhone(),
                ];
            }
        }

        $cityRepository = $this->entityManager->getRepository(City::class);
        $userRepository = $this->entityManager->getRepository(User::class);

        // Создать недостающие сущности "City"
        $existingCities = $cityRepository->findByIds($newCityIds);
        foreach ($existingCities as $existingCity) {
            unset($newCities[$existingCity->getId()]);
        }

        foreach ($newCities as $newCity) {
            $cityEntity = new City($newCity['name'], $newCity['country']);
            $this->entityManager->persist($cityEntity);
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        // Создать недостающие сущности "User"
        foreach (chunk($newUsers, BatchConstant::SIZE) as $batchUsers) {
            $newUserIds = array_column($batchUsers, 'id');
            $existingUsers = $userRepository->findByIds($newUserIds);
            foreach ($existingUsers as $existingUser) {
                unset($newUsers[$existingUser->getId()]);
            }
        }

        foreach (chunk($newUsers, BatchConstant::SIZE) as $batchUsers) {
            foreach ($batchUsers as $batchUser) {
                $userEntity = new User($batchUser['name'], $batchUser['phone']);
                $this->entityManager->persist($userEntity);
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        // Создать связи "City" -> "User"
        foreach ($cities as $city) {
            $userIds = [];
            foreach ($city->getUsers() as $user) {
                $userIds[] = $user->getId();
            }

            $cityEntity = $cityRepository->find($city->getId());
            foreach (chunk($userIds, BatchConstant::SIZE) as $batchUserIds) {
                $users = $userRepository->findByIds($batchUserIds);
                foreach ($users as $user) {
                    $cityEntity->addUser($user);
                }

                $this->entityManager->flush();
            }

            $this->entityManager->clear();
        }
    }
}
