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

    // Vous pouvez conserver les autres méthodes commentées si vous pensez les utiliser plus tard
}
