<?php

declare(strict_types=1);

namespace App\DTO;

use Spatie\Typed\Collection;
use Spatie\Typed\T;

/**
 * The "GenerateCityDTO" class.
 */
final class ParseCityDTO
{
    /**
     * @var string
     */
    private string $id;
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $country;
    /**
     * @var Collection|ParseUserDTO[]
     */
    private $users;

    public function __construct(string $id, string $name, string $country, array $users)
    {
        $this->id = $id;
        $this->name = $name;
        $this->country = $country;
        $this->users = (new Collection(T::generic(ParseUserDTO::class)))->set($users);
    }

    public function getId(): string
    {
        return $this->id;
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
     * @return Collection|ParseUserDTO[]
     */
    public function getUsers()
    {
        return $this->users;
    }
}
