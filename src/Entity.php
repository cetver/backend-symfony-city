<?php declare(strict_types=1);

namespace App;

abstract class Entity implements Interfaces\EntityInterface
{
    private ?int $id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    public function __toString() : string
    {
        return (string) json_encode($this->toArray(),JSON_THROW_ON_ERROR);
    }
}
