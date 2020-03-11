<?php declare(strict_types=1);

namespace App\Interfaces;

interface EntityInterface
{
    public function getId(): ?int;
    public function setId(int $id) : void;

    public function toArray(bool $withRelations = true);
    public static function getSource() : string;
}
