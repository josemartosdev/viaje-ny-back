<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Trip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Trip>
 */
class TripRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trip::class);
    }

    /** @return list<Trip> */
    public function search(?string $q): array
    {
        $qb = $this->createQueryBuilder('t')->orderBy('t.startDate', 'ASC');

        if ($q !== null && $q !== '') {
            $qb
                ->andWhere('LOWER(t.name) LIKE :q OR LOWER(t.city) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
