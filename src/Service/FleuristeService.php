<?php

namespace App\Service;

use App\Entity\Fleur;
use App\Entity\Fleuriste;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class FleuristeService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Récupère ou crée le profil fleuriste d'un utilisateur
     */
    public function getOrCreateFleuriste(User $user, bool $redirect = false): Fleuriste
    {
        $fleuriste = $user->getFleuriste();

        if ($fleuriste !== null) {
            return $fleuriste;
        }

        $fleuriste = new Fleuriste();
        $fleuriste->setUser($user);
        $fleuriste->setNom('Mon magasin');
        $fleuriste->setEmail($user->getEmail());

        $this->entityManager->persist($fleuriste);
        $this->entityManager->flush();

        return $fleuriste;
    }

    /**
     * Vérifie que le fleuriste est propriétaire de la fleur
     *
     * @throws \RuntimeException Si le fleuriste n'est pas propriétaire
     */
    public function checkFlowerOwnership(Fleur $fleur, User $user): void
    {
        $fleuriste = $user->getFleuriste();

        if (!$fleuriste) {
            throw new \RuntimeException('Vous devez avoir un profil fleuriste pour accéder à cette ressource.');
        }

        if ($fleur->getFleuriste() !== $fleuriste) {
            throw new \RuntimeException('Vous n\'êtes pas autorisé à modifier cette fleur.');
        }
    }

    /**
     * Réapprovisionne une fleur
     */
    public function reapprovisionner(Fleur $fleur, int $quantite): void
    {
        if ($quantite > 0) {
            $fleur->setStock($fleur->getStock() + $quantite);
            $this->entityManager->flush();
        }
    }

    /**
     * Ajoute une fleur du catalogue global au catalogue du fleuriste
     *
     * @return bool True si ajoutée, false si déjà existante
     */
    public function addFleurToCatalogue(Fleuriste $fleuriste, Fleur $fleur): bool
    {
        $fleurExistante = $fleuriste->getFleurs()->exists(
            fn($key, $existingFleur) => $existingFleur->getNom() === $fleur->getNom()
        );

        if ($fleurExistante) {
            return false;
        }

        $newFleur = (new Fleur())
            ->setNom($fleur->getNom())
            ->setDescription($fleur->getDescription())
            ->setPrix($fleur->getPrix())
            ->setThc($fleur->getThc())
            ->setStock(0)
            ->setFleuriste($fleuriste);

        $this->entityManager->persist($newFleur);
        $this->entityManager->flush();

        return true;
    }
}
