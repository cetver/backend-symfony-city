<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * The "GenerateUserDTO" class.
 */
final class ParseUserDTO
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
    private string $phone;

    public function __construct(string $id, string $name, string $phone)
    {
        $this->id = $id;
        $this->name = $name;
        $this->phone = $phone;
    }

    public function getId(): string
    {
        return $this->id;
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
