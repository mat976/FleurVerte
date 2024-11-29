<?php

namespace App\Repository;

use App\Entity\FleuristeImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour gérer les images des fleuristes
 * 
 * Ce repository fournit des méthodes pour :
 * - Rechercher des images par fleuriste
 * - Gérer la persistance des images
 * 
 * @extends ServiceEntityRepository<FleuristeImage>
 * 
 * @method FleuristeImage|null find($id, $lockMode = null, $lockVersion = null)
 * @method FleuristeImage|null findOneBy(array $criteria, array $orderBy = null)
 * @method FleuristeImage[]    findAll()
 * @method FleuristeImage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FleuristeImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FleuristeImage::class);
    }

    public function save(FleuristeImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FleuristeImage $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
