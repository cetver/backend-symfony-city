<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\City;
use App\Entity\User;
use App\Repository\CityRepository;
use App\Repository\UserRepository;

class Parser
{
    private $cityRepository;
    private $userRepository;
    private $counter = 0;

    public function __construct(CityRepository $cityRepository, UserRepository $userRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(string $file) : ?int
    {
        $oFile = fopen(VAR_DIR . $file, 'r');
        if ($oFile === false) {
            return null;
        }

        while (($line = fgets($oFile)) !== false) {
            $this->processLine($line);
        }

        if (!feof($oFile)) {
            throw new \DomainException('Конец файла не достигнут');
        }
        fclose($oFile);

        return $this->counter;
    }

    private function processLine(string $line) : bool
    {
        $data = json_decode(trim($line), false, 13, JSON_THROW_ON_ERROR);
        $city = $this->saveData($data);
        if (null !== $city) {
            $this->counter++;
            return true;
        }

        return false;
    }
    private function saveData(\stdClass $data): ?City
    {
        $city = new City($data->name, $data->country);
        if ($this->cityRepository->insert($city) === false) {
            return null;
        }

        foreach($data->users as $user) {
            $user = new User($user->name, $user->phone);
            if ($this->userRepository->insert($user, ['city_id' => $city->getId()]) !== false) {
                $city->attachUser($user);
            }
        }

        return $city;
    }
}
