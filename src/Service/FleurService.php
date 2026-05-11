<?php

namespace App\Service;

use App\Repository\FleurRepository;

class FleurService
{
    public function __construct(
        private readonly FleurRepository $fleurRepository
    ) {}

    /**
     * Recherche et trie les fleurs selon les critères
     *
     * @return array<\App\Entity\Fleur>
     */
    public function search(string $search, string $sort, ?string $tagId): array
    {
        if (!empty($search) || !empty($tagId)) {
            return $this->fleurRepository->searchByTermOrTag($search, [
                'sort' => $sort,
                'tag_id' => $tagId,
            ]);
        }

        $queryBuilder = $this->fleurRepository->createQueryBuilder('f')
            ->leftJoin('f.tags', 't')
            ->addSelect('t')
            ->leftJoin('f.fleuriste', 'fl')
            ->addSelect('fl');

        match ($sort) {
            'name_desc' => $queryBuilder->orderBy('f.nom', 'DESC'),
            'price_asc' => $queryBuilder->orderBy('f.prix', 'ASC'),
            'price_desc' => $queryBuilder->orderBy('f.prix', 'DESC'),
            default => $queryBuilder->orderBy('f.nom', 'ASC'),
        };

        return $queryBuilder->getQuery()->getResult();
    }
}
