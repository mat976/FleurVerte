<?php

namespace App\Autocomplete;

use App\Entity\Fleur;
use App\Repository\FleurRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Autocomplete\EntityAutocompleteFieldInterface;

/**
 * Service d'autocomplétion pour les produits
 */
class FleurAutocompleteField implements EntityAutocompleteFieldInterface
{
    public function __construct(
        private FleurRepository $fleurRepository,
        private Security $security
    ) {
    }

    public function getEntityClass(): string
    {
        return Fleur::class;
    }

    public function getFilteredQueryBuilder(string $query): mixed
    {
        return $this->fleurRepository->createQueryBuilder('f')
            ->where('f.nom LIKE :search OR f.description LIKE :search')
            ->setParameter('search', '%' . $query . '%')
            ->orderBy('f.nom', 'ASC');
    }

    public function isGranted(): bool
    {
        // Tout le monde peut rechercher des produits
        return true;
    }
}