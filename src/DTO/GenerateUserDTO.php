<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * The "GenerateUserDTO" class.
 */
final class GenerateUserDTO
{
    /**
     * @var string
     */
    private string $name;
    /**
     * @var string
     */
    private string $phone;

    public function __construct(string $name, string $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
