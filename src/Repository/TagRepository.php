<?php

namespace App\Repository;

use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tag>
 *
 * @method Tag|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tag|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tag[]    findAll()
 * @method Tag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tag::class);
    }

    /**
     * Trouve tous les tags triés par nom
     * 
     * @return Tag[] Liste des tags triés par ordre alphabétique
     */
    public function findAllSorted(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Recherche des tags par nom (recherche partielle)
     * 
     * @param string $term Le terme de recherche
     * @return Tag[] Liste des tags correspondants
     */
    public function findBySearchTerm(string $term): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.nom LIKE :term')
            ->setParameter('term', '%' . $term . '%')
            ->orderBy('t.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
