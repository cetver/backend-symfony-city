<?php declare(strict_types=1);

namespace App\Interfaces;

interface ProviderInterface
{
    public function insert(EntityInterface $entity, array $fields = []) : ?int;
    /** @var EntityInterface $entity */
    public function clear(string $entity) : bool;
}
