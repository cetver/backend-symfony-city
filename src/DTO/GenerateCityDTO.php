<?php

declare(strict_types=1);

namespace App\DTO;

use Spatie\Typed\Collection;
use Spatie\Typed\T;

/**
 * The "GenerateCityDTO" class.
 */
final class GenerateCityDTO
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $country;
    /**
     * @var Collection
     */
    private Collection $users;

    public function __construct(string $name, string $country, array $users)
    {
        $this->name = $name;
        $this->country = $country;
        $this->users = (new Collection(T::generic(GenerateUserDTO::class)))->set($users);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return Collection|GenerateUserDTO[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }
}
