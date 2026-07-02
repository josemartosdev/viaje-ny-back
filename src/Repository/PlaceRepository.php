<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Place;
use App\Enum\PlaceType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Place>
 */
class PlaceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Place::class);
    }

    /** @return list<Place> */
    public function findByFilters(?PlaceType $type, ?int $minPriceLevel, ?int $maxPriceLevel, ?string $q): array
    {
        $qb = $this->createQueryBuilder('p')->orderBy('p.name', 'ASC');

        if ($type !== null) {
            $qb->andWhere('p.type = :type')->setParameter('type', $type);
        }

        if ($minPriceLevel !== null) {
            $qb->andWhere('p.priceLevel >= :minLevel')->setParameter('minLevel', $minPriceLevel);
        }

        if ($maxPriceLevel !== null) {
            $qb->andWhere('p.priceLevel <= :maxLevel')->setParameter('maxLevel', $maxPriceLevel);
        }

        if ($q !== null && $q !== '') {
            $qb->andWhere('LOWER(p.name) LIKE :q')->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
