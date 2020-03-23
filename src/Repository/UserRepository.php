<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserRepository
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return User[]
     */
    public function findByIds(array $ids)
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb
            ->select('t')
            ->from(User::class, 't')
            ->where($qb->expr()->in('t.id', $ids))
            ->getQuery()
            ->getResult();
    }
}
