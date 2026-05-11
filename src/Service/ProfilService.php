<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Fleuriste;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SluggerInterface $slugger,
        private readonly string $avatarsDirectory
    ) {}

    /**
     * Gère le changement de rôle fleuriste/client
     */
    public function handleRoleToggle(User $user, string $wantToBeFleuriste, ?string $shopName, ?string $siret = null): ?string
    {
        if ($wantToBeFleuriste === '1') {
            return $this->activateFleuriste($user, $shopName, $siret);
        }

        $this->deactivateFleuriste($user);
        return null;
    }

    /**
     * Active ou crée le profil fleuriste
     *
     * Crée le profil en statut "en_attente" (non actif) jusqu'à validation admin.
     *
     * @return string|null Message d'erreur si échec
     */
    private function activateFleuriste(User $user, ?string $shopName, ?string $siret): ?string
    {
        if ($user->getFleuriste() === null) {
            if (empty($shopName)) {
                return 'Veuillez entrer un nom de boutique pour devenir fleuriste.';
            }
            if (empty($siret) || !preg_match('/^\d{14}$/', $siret)) {
                return 'Le numéro SIRET est obligatoire et doit contenir exactement 14 chiffres.';
            }

            $fleuriste = new Fleuriste();
            $fleuriste->setUser($user);
            $fleuriste->setNom($shopName);
            $fleuriste->setSiret($siret);
            $fleuriste->setStatut(Fleuriste::STATUT_EN_ATTENTE);
            $fleuriste->setActif(false); // en attente de validation admin
            $this->entityManager->persist($fleuriste);
        } else {
            $existing = $user->getFleuriste();
            // Si déjà validé, permettre la mise à jour du nom
            if ($shopName) {
                $existing->setNom($shopName);
            }
            // Si SIRET fourni et différent, réinitialiser en attente
            if ($siret && $existing->getSiret() !== $siret) {
                if (!preg_match('/^\d{14}$/', $siret)) {
                    return 'Le numéro SIRET doit contenir exactement 14 chiffres.';
                }
                $existing->setSiret($siret);
                $existing->setStatut(Fleuriste::STATUT_EN_ATTENTE);
                $existing->setActif(false);
            }
        }

        return null;
    }

    /**
     * Désactive le profil fleuriste et crée un profil client si nécessaire
     */
    private function deactivateFleuriste(User $user): void
    {
        if ($user->getFleuriste() !== null) {
            $user->getFleuriste()->setActif(false);
        }

        if ($user->getClient() === null) {
            $client = new Client();
            $client->setUser($user);
            $this->entityManager->persist($client);
        }
    }

    /**
     * Gère l'upload ou la sélection d'avatar
     *
     * @return string|null Message d'erreur si échec
     */
    public function handleAvatar(User $user, ?UploadedFile $avatarFile, ?string $avatarName): ?string
    {
        if ($avatarFile) {
            return $this->uploadAvatar($user, $avatarFile);
        }

        if ($avatarName) {
            $user->setAvatarName($avatarName);
        }

        return null;
    }

    /**
     * Upload un fichier avatar
     *
     * @return string|null Message d'erreur si échec
     */
    private function uploadAvatar(User $user, UploadedFile $file): ?string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->avatarsDirectory, $newFilename);
            $user->setAvatarName($newFilename);
            return null;
        } catch (\Exception $e) {
            return 'Erreur lors du téléchargement de l\'avatar.';
        }
    }
}
