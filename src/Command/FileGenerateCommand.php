<?php

declare(strict_types=1);

namespace App\Command;

use App\Constant\EnvConstant;
use App\DTO\FileGenerateCommandOptionsDTO;
use App\Factory\ProgressBarFactory;
use App\Service\CityGeneratorService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * The "FileGenerateCommand" class.
 */
class FileGenerateCommand extends Command
{
    use LockableTrait;
    protected static $defaultName = 'file:generate';
    /**
     * @var string
     */
    private string $env;
    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;
    /**
     * @var CityGeneratorService
     */
    private CityGeneratorService $cityGenerator;
    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;
    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;
    /**
     * @var ProgressBarFactory
     */
    private ProgressBarFactory $progressBarFactory;
    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    public function __construct(
        string $env,
        ValidatorInterface $validator,
        CityGeneratorService $cityGenerator,
        Filesystem $filesystem,
        SerializerInterface $serializer,
        ProgressBarFactory $progressBarFactory,
        LoggerInterface $logger
    ) {
        $this->env = $env;
        $this->validator = $validator;
        $this->cityGenerator = $cityGenerator;
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
        $this->progressBarFactory = $progressBarFactory;
        $this->logger = $logger;
        parent::__construct(static::$defaultName);
    }

    protected function configure()
    {
        $this
            ->addOption('filepath', null, InputOption::VALUE_OPTIONAL, 'Путь к файлу')
            ->addOption('lines', null, InputOption::VALUE_OPTIONAL, 'Количество строк')
            ->setDescription('Сгенерировать файл с данными')
            ->setHelp(
                <<<EOF
Команда <info>%command.name%</info> генерирует заданный файл с данными, с определенным количестом строк.
Пример:
<info>php %command.full_name% --filepath="/tmp/data" --lines="100"</info>
EOF
            );
    }

    public function isHidden()
    {
        return EnvConstant::PROD === $this->env;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $this->logger->error('The command is already running in another process');

            return 1;
        }

        $filepath = $input->getOption('filepath');
        $lines = $input->getOption('lines');
        $dto = new FileGenerateCommandOptionsDTO($filepath, $lines);
        $errors = $this->validator->validate($dto);
        if ($errors->count() > 0) {
            $this->logger->error("\n".$errors);

            return 1;
        }

        $progressBar = $this->progressBarFactory->create($output, $dto->getLines());
        try {
            $tempFilepath = $this->filesystem->tempnam(dirname($filepath), $this->getName());
            $cities = $this->cityGenerator->cities($dto->getLines());
            foreach ($cities as $city) {
                $content = $this->serializer->serialize($city, JsonEncoder::FORMAT)."\n";
                $this->filesystem->appendToFile($tempFilepath, $content);
                $progressBar->advance();
            }

            $this->filesystem->rename($tempFilepath, $filepath, true);
        } catch (IOException $e) {
            $progressBar->clear();
            $this->logger->critical($e->getMessage());

            return 1;
        }
        $progressBar->finish();

        return 0;
    }
}
