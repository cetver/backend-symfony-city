<?php declare(strict_types=1);

namespace App\Interfaces;
use PDO;

interface RepositoryInterface
{
    public function __construct(ProviderInterface $provider, string $entity);
    public function bindEntity(string $entity) : void;
    public function clear() : bool;
    public function insert(EntityInterface &$entity) : bool;
}
