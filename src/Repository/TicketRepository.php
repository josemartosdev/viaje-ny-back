<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ticket;
use App\Enum\TicketType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ticket>
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    /** @return list<Ticket> */
    public function findByFilters(?int $dayId, ?TicketType $type, ?string $q): array
    {
        $qb = $this->createQueryBuilder('t')
            ->leftJoin('t.day', 'd')->addSelect('d')
            ->leftJoin('t.activity', 'a')->addSelect('a')
            ->orderBy('t.id', 'ASC');

        if ($dayId !== null) {
            $qb->andWhere('d.id = :dayId')->setParameter('dayId', $dayId);
        }

        if ($type !== null) {
            $qb->andWhere('t.type = :type')->setParameter('type', $type);
        }

        if ($q !== null && $q !== '') {
            $qb
                ->andWhere('LOWER(t.title) LIKE :q OR LOWER(COALESCE(t.provider, \"\")) LIKE :q')
                ->setParameter('q', '%' . mb_strtolower($q) . '%');
        }

        return $qb->getQuery()->getResult();
    }
}
