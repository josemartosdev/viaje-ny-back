<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Activity;
use App\Enum\ActivityStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Activity>
 */
class ActivityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Activity::class);
    }

    /** @return list<Activity> */
    public function findByFilters(?int $dayId, ?ActivityStatus $status, ?string $category, ?string $q): array
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.day', 'd')->addSelect('d')
            ->leftJoin('a.place', 'p')->addSelect('p')
            ->orderBy('a.id', 'ASC');

        if ($dayId !== null) {
            $qb->andWhere('d.id = :dayId')->setParameter('dayId', $dayId);
        }

        if ($status !== null) {
            $qb->andWhere('a.status = :status')->setParameter('status', $status);
        }

        if ($category !== null && $category !== '') {
            $qb->andWhere('LOWER(a.category) = :category')->setParameter('category', mb_strtolower($category));
        }

        if ($q !== null && $q !== '') {
            $qb->andWhere('LOWER(a.title) LIKE :q')->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
