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
            case 'thc_asc':
                $qb->orderBy('f.thc', 'ASC');
                break;
            case 'thc_desc':
                $qb->orderBy('f.thc', 'DESC');
                break;
            case 'name_asc':
            default:
                $qb->orderBy('f.nom', 'ASC');
                break;
        }

        return $qb->getQuery()->getResult();
    }
}
