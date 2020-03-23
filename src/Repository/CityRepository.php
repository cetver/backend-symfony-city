<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;

class CityRepository
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
     * @return City|object|null
     */
    public function find(string $id)
    {
        return $this->entityManager->find(City::class, $id);
    }

    /**
     * @return City[]
     */
    public function findByIds(array $ids)
    {
        $qb = $this->entityManager->createQueryBuilder();

        return $qb
            ->select('t')
            ->from(City::class, 't')
            ->where($qb->expr()->in('t.id', $ids))
            ->getQuery()
            ->getResult();
    }

    public function countUsers()
    {
        $stmt = $this->entityManager
            ->getConnection()
            ->prepare('
                SELECT c.name   City,
                       COUNT(cu.user_id) \'Count Users\'
                FROM cities c
                LEFT JOIN cities_users cu
                ON cu.city_id = c.id
                GROUP BY c.name                
                ORDER BY COUNT(cu.user_id) DESC,
                         c.name
            ');
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
