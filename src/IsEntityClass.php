<?php declare(strict_types=1);

namespace App;

use App\Interfaces\EntityInterface;

trait IsEntityClass
{
    public function isEntityClass(string $entity) : bool
    {
        if (\in_array(EntityInterface::class, class_implements($entity), true) === false) {
            throw new \InvalidArgumentException(sprintf(
                'Ожидается имя класса сущности с интерфейсом %s', EntityInterface::class
            ));
        }

        return true;
    }
}
