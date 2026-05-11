<?php

namespace App\Repository;

use App\Entity\Fleuriste;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FleuristeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fleuriste::class);
    }

    public function findFleuristesWithAddress(): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.adresse', 'a')
            ->getQuery()
            ->getResult();
    }

    /**
     * Retourne les fleuristes ayant un statut donné (ex: en_attente)
     *
     * @return Fleuriste[]
     */
    public function findByStatut(string $statut): array
    {
        return $this->createQueryBuilder('f')
            ->innerJoin('f.user', 'u')
            ->addSelect('u')
            ->where('f.statut = :statut')
            ->setParameter('statut', $statut)
            ->orderBy('f.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
