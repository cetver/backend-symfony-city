<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity as BaseEntity;

class User extends BaseEntity
{
    private string $name;
    private string $phone;

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function __construct(string $name, string $phone)
    {
        $this->name = $name;
        $this->phone = $phone;
    }

    public function toArray(bool $withRelations = true) : array
    {
        return [
            'name' => $this->getName(),
            'phone' => $this->getPhone()
        ];
    }

    public static function getSource() : string
    {
        return 'users';
    }
}
