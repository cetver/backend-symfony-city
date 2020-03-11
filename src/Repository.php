<?php declare(strict_types=1);

namespace App;

use App\Interfaces\EntityInterface;
use App\Interfaces\ProviderInterface;
use App\Interfaces\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    use IsEntityClass;

    protected ProviderInterface $provider;

    /** @var EntityInterface $entity */
    protected string $entity;

    public function __construct(ProviderInterface $provider, string $entity)
    {
        $this->provider = $provider;
        $this->bindEntity($entity);
    }

    public function bindEntity(string $entity) : void
    {
        $this->isEntityClass($entity);

        $this->entity = $entity;
    }

    public function clear() : bool
    {
        return $this->provider->clear($this->entity);
    }

    public function insert(EntityInterface &$entity, array $fields = []) : bool
    {
        if (($entity instanceof $this->entity) === false) {
            throw new \InvalidArgumentException(sprintf('Ожидается тип %s', $this->entity));
        }

        $id = $this->provider->insert($entity, $fields);

        if (null !== $id) {
            $entity->setId($id);
            return true;
        }

        return false;
    }
}
