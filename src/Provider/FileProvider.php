<?php declare(strict_types=1);

namespace App\Provider;

use App\Interfaces\EntityInterface;
use App\Interfaces\ProviderInterface;
use App\IsEntityClass;

class FileProvider implements ProviderInterface
{
    use IsEntityClass;

    private string $storage = 'db/';

    public function __construct()
    {
        $this->makeStorage();
    }

    public function insert(EntityInterface $entity, array $fields = []) : ?int
    {
        $storage = $this->resolveStorage($entity::getSource());
        if (!is_dir($storage)) {
            @mkdir($storage);
        }

        $id = $this->resolveNextId($storage);

        $data = array_merge($entity->toArray(false), $fields);
        if (file_put_contents($storage.$id, serialize($data))) {
            return $id;
        }

        return null;
    }

    /**
     * @param EntityInterface $entity
     */
    public function clear(string $entity): bool
    {
        $this->isEntityClass($entity);

        foreach(glob($this->resolveStorage($entity::getSource()).'*') as $file)
        {
            unlink($file);
        }

        return true;
    }

    private function makeStorage()
    {
        $storage = $this->resolveStorage('');
        if (is_dir($storage)) {
            @unlink($storage);
        }

        @mkdir($storage);
    }

    private function resolveStorage(string $source = '')
    {
        return rtrim(VAR_DIR.$this->storage.$source, '/').'/';
    }

    private function resolveNextId(string $dir) : int
    {
        $id = 0;

        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file->isDot()) {
                continue;
            }

            $id = max((int)$file->getFilename(), $id);
        }

        return $id + 1;
    }
}
