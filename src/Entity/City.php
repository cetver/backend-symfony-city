<?php declare(strict_types=1);

namespace App\Entity;

use App\Entity as BaseEntity;

class City extends BaseEntity
{
    private string $name;
    private string $country;
    /** @var User[]  */
    private array $users = [];

    public function __construct(string $name, string $state)
    {
        $this->name = $name;
        $this->country = $state;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function attachUser(User $user) : void
    {
        $this->users[] = $user;
    }

    public function getUsers() : array
    {
        return $this->users;
    }

    public function toArray(bool $withRelations = true) : array
    {
        $data = [
            'name' => $this->getName(),
            'country' => $this->getCountry()
        ];

        if ($withRelations) {
            $data['users'] = array_map(fn(User $user) => $user->toArray(), $this->getUsers());
        }

        return $data;
    }

    public static function getSource() : string
    {
        return 'cities';
    }
}
