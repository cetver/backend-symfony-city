<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\GenerateCityDTO;
use App\DTO\GenerateUserDTO;
use Faker\Generator;
use function iter\toArray;

/**
 * The "CityGeneratorService" class.
 */
class CityGeneratorService
{
    private const MIN_USERS_COUNT = 1;
    private const MAX_USERS_COUNT = 100;
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(Generator $faker)
    {
        $this->faker = $faker;
    }

    /**
     * @return \Generator|GenerateCityDTO
     */
    public function cities(int $limit)
    {
        for ($i = 0; $i < $limit; ++$i) {
            $users = toArray($this->users());

            yield new GenerateCityDTO($this->faker->city, $this->faker->country, $users);
        }
    }

    /**
     * @return \Generator|GenerateUserDTO
     */
    private function users()
    {
        $countUsers = $this->faker->numberBetween(self::MIN_USERS_COUNT, self::MAX_USERS_COUNT);
        for ($i = 0; $i < $countUsers; ++$i) {
            yield new GenerateUserDTO($this->faker->name, $this->faker->phoneNumber);
        }
    }
}
