<?php

namespace App\Repository;

use App\Entity\Fleur;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Fleur>
 */
class FleurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Fleur::class);
    }

    /**
     * Recherche des fleurs par nom, description ou tags
     * 
     * @param string $query Terme de recherche
     * @param array $options Options de tri et filtrage
     * @return Fleur[] Liste des fleurs correspondant aux critères
     */
    public function searchByTermOrTag(string $query, array $options = []): array
    {
        $qb = $this->createQueryBuilder('f')
            ->leftJoin('f.tags', 't')
            ->addSelect('t')
            ->leftJoin('f.fleuriste', 'fl')
            ->addSelect('fl')
            ->distinct();

        // Recherche par nom, description ou tag
        $qb->andWhere('f.nom LIKE :query OR f.description LIKE :query OR t.nom LIKE :query')
            ->setParameter('query', '%' . $query . '%');

        // Filtrage par tag spécifique si fourni
        if (!empty($options['tag_id'])) {
            $qb->andWhere(':tag MEMBER OF f.tags')
                ->setParameter('tag', $options['tag_id']);
        }

        // Appliquer le tri
        $sort = $options['sort'] ?? 'name_asc';
        switch ($sort) {
            case 'name_desc':
                $qb->orderBy('f.nom', 'DESC');
                break;
            case 'price_asc':
                $qb->orderBy('f.prix', 'ASC');
                break;
            case 'price_desc':
                $qb->orderBy('f.prix', 'DESC');
                break;
            case 'promo':
                $qb->orderBy('f.promoPercent', 'DESC');
                break;
            case 'name_asc':
            default:
                $qb->orderBy('f.nom', 'ASC');
                break;
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Fleur[]
     */
    public function findActivePromos(int $limit = 6): array
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('f')
            ->leftJoin('f.tags', 't')
            ->addSelect('t')
            ->leftJoin('f.fleuriste', 'fl')
            ->addSelect('fl')
            ->where('f.promoActive = true')
            ->andWhere('f.promoPercent > 0')
            ->andWhere('f.promoStart IS NULL OR f.promoStart <= :now')
            ->andWhere('f.promoEnd IS NULL OR f.promoEnd >= :now')
            ->andWhere('f.stock > 0')
            ->setParameter('now', $now)
            ->orderBy('f.promoPercent', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Fleur[]
     */
    public function findLatest(int $limit = 6): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin('f.tags', 't')
            ->addSelect('t')
            ->leftJoin('f.fleuriste', 'fl')
            ->addSelect('fl')
            ->where('f.stock > 0')
            ->orderBy('f.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
