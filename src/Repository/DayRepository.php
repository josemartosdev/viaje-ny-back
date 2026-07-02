<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Day;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Day>
 */
class DayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Day::class);
    }

    /** @return list<Day> */
    public function search(?string $q, ?int $tripId): array
    {
        $qb = $this->createQueryBuilder('d')
            ->leftJoin('d.trip', 't')->addSelect('t')
            ->orderBy('d.date', 'ASC');

        if ($q !== null && $q !== '') {
            $qb
                ->andWhere('LOWER(d.title) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        if ($tripId !== null) {
            $qb->andWhere('t.id = :tripId')->setParameter('tripId', $tripId);
        }

        return $qb->getQuery()->getResult();
    }
}
