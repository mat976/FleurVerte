<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function countByStatut(string $statut): int
    {
        return (int)$this->createQueryBuilder('c')
            ->select('COUNT(c.id)')
            ->andWhere('c.statut = :statut')
            ->setParameter('statut', $statut)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function sumTotal(): float
    {
        return (float)($this->createQueryBuilder('c')
            ->select('SUM(c.total)')
            ->andWhere('c.statut != :annulee')
            ->setParameter('annulee', Commande::STATUT_ANNULEE)
            ->getQuery()
            ->getSingleScalarResult() ?? 0);
    }
}
