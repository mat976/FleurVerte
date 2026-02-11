<?php

namespace App\Service;

use App\Entity\Commentaire;
use App\Entity\Fleur;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CommentaireService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Crée un commentaire sur une fleur
     */
    public function create(User $user, Fleur $fleur, Commentaire $commentaire): void
    {
        $commentaire->setUser($user);
        $commentaire->setFleur($fleur);
        $commentaire->setDateCreation(new \DateTime());

        $this->entityManager->persist($commentaire);
        $this->entityManager->flush();
    }

    /**
     * Supprime un commentaire si l'utilisateur en est propriétaire ou admin
     *
     * @return bool True si supprimé, false si non autorisé
     */
    public function delete(Commentaire $commentaire, User $user, bool $isAdmin): bool
    {
        if ($commentaire->getUser() !== $user && !$isAdmin) {
            return false;
        }

        $this->entityManager->remove($commentaire);
        $this->entityManager->flush();

        return true;
    }
}
