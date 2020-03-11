<?php declare(strict_types=1);

namespace App\Services;

use App\Entity\City;
use App\Entity\User;
use Faker\{Factory as FakerFactory, Generator as FakerGenerator};

class Generator
{
    private FakerGenerator $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create('ru_RU');
    }

    public function __invoke(string $file, int $lines): ?int
    {
        @unlink($file);

        $oFile = fopen(VAR_DIR . $file, 'w');
        if ($oFile === false) {
            return null;
        }

        for ($i = 1; $i <= $lines; $i++) {
            $city = new City($this->faker->city, $this->faker->country);
            $users = $this->faker->numberBetween(1, 100);
            do {
                $city->attachUser(
                    new User($this->faker->name, $this->faker->phoneNumber)
                );
            } while (--$users > 0);

            $this->save($city, $oFile);
        }

        fclose($oFile);

        $size = filesize(VAR_DIR . $file);

        return $size === false ? null : $size;
    }

    private function save(City $city, $oFile) : bool
    {
        return is_resource($oFile) && fwrite($oFile, (string)$city . PHP_EOL);
    }
}
