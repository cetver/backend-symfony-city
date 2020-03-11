<?php declare(strict_types=1);

namespace App\Provider;
use App\Interfaces\EntityInterface;
use App\Interfaces\ProviderInterface;
use App\IsEntityClass;
use PDO;

class DatabaseProvider implements ProviderInterface
{
    use IsEntityClass;

    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert(EntityInterface $entity, array $fields = []) : ?int
    {
        $data = array_merge($entity->toArray(false), $fields);

        $flag = $this->pdo->prepare($this->makeQuery($entity, array_keys($data)))
            ->execute($data);

        if ($flag) {
            return (int)$this->pdo->lastInsertId();
        }

        return null;
    }

    private function makeQuery(EntityInterface $entity, array $keys = []) : string
    {
        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $entity::getSource(),
            implode(',', array_map(fn($key) => '`'.$key.'`', $keys)),
            implode(',', array_map(fn($key) => ':'.$key, $keys))
        );
    }

    public function clear(string $entity): bool
    {
        $this->isEntityClass($entity);

        return (bool) $this->pdo->query('DELETE FROM ' . $entity::getSource());
    }


}
