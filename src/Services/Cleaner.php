<?php declare(strict_types=1);

namespace App\Services;

use App\Interfaces\RepositoryInterface;

class Cleaner
{
    /** @var RepositoryInterface[]  */
    private array $repositories;

    public function addRepository(RepositoryInterface $repository) : void
    {
        $this->repositories[] = $repository;
    }

    public function clearRepositories() : void
    {
        foreach ($this->repositories as $repository)
        {
            $repository->clear();
        }
    }
}
