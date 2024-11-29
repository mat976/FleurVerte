<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur gérant les opérations CRUD sur les adresses des utilisateurs
 */
#[Route('/adresse')]
#[IsGranted('ROLE_USER')]
class AdresseController extends AbstractController
{
    // Messages flash constants
    private const MESSAGE_ACCESS_DENIED = 'Vous n\'avez pas accès à cette adresse';
    private const MESSAGE_ADDED = 'Adresse ajoutée avec succès';
    private const MESSAGE_EDITED = 'Adresse modifiée avec succès';
    private const MESSAGE_DELETED = 'Adresse supprimée avec succès';
    private const MESSAGE_MAIN_UPDATED = 'Adresse principale mise à jour avec succès';
    private const MESSAGE_CANT_DELETE_MAIN = 'Vous ne pouvez pas supprimer l\'adresse principale. Veuillez d\'abord définir une autre adresse comme principale.';

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Crée une nouvelle adresse pour l'utilisateur
     * 
     * @param Request $request La requête HTTP
     */
    #[Route('/new', name: 'app_adresse_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $adresse = (new Adresse())->setUser($this->getUser());
        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleMainAddress($adresse);
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();

            $this->addFlash('success', self::MESSAGE_ADDED);
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('adresse/new.html.twig', [
            'adresse' => $adresse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifie une adresse existante
     * 
     * @param Request $request La requête HTTP
     * @param Adresse $adresse L'adresse à modifier
     */
    #[Route('/{id}/edit', name: 'app_adresse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adresse $adresse): Response
    {
        $this->checkAddressOwnership($adresse);

        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleMainAddress($adresse);
            $this->entityManager->flush();

            $this->addFlash('success', self::MESSAGE_EDITED);
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('adresse/edit.html.twig', [
            'adresse' => $adresse,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprime une adresse
     * 
     * @param Request $request La requête HTTP
     * @param Adresse $adresse L'adresse à supprimer
     */
    #[Route('/{id}/delete', name: 'app_adresse_delete', methods: ['POST'])]
    public function delete(Request $request, Adresse $adresse): Response
    {
        $this->checkAddressOwnership($adresse);

        if ($this->cannotDeleteMainAddress($adresse)) {
            $this->addFlash('error', self::MESSAGE_CANT_DELETE_MAIN);
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($adresse);
            $this->entityManager->flush();
            $this->addFlash('success', self::MESSAGE_DELETED);
        }

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }

    /**
     * Définit une adresse comme principale
     * 
     * @param Request $request La requête HTTP
     * @param Adresse $adresse L'adresse à définir comme principale
     */
    #[Route('/{id}/set-principale', name: 'app_adresse_set_principale', methods: ['POST'])]
    public function setPrincipale(Request $request, Adresse $adresse): Response
    {
        $this->checkAddressOwnership($adresse);

        if ($this->isCsrfTokenValid('set-principale'.$adresse->getId(), $request->request->get('_token'))) {
            $this->updateMainAddress($adresse);
            $this->entityManager->flush();
            $this->addFlash('success', self::MESSAGE_MAIN_UPDATED);
        }

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }

    /**
     * Vérifie que l'utilisateur est propriétaire de l'adresse
     * 
     * @throws AccessDeniedException Si l'utilisateur n'est pas propriétaire
     */
    private function checkAddressOwnership(Adresse $adresse): void
    {
        if ($adresse->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException(self::MESSAGE_ACCESS_DENIED);
        }
    }

    /**
     * Gère la logique d'adresse principale lors de l'ajout/modification
     */
    private function handleMainAddress(Adresse $adresse): void
    {
        if ($this->getUser()->getAdresses()->isEmpty()) {
            $adresse->setPrincipale(true);
        } elseif ($adresse->isPrincipale()) {
            $this->updateMainAddress($adresse);
        }
    }

    /**
     * Met à jour le statut d'adresse principale
     */
    private function updateMainAddress(Adresse $mainAddress): void
    {
        foreach ($this->getUser()->getAdresses() as $address) {
            $address->setPrincipale($address === $mainAddress);
        }
    }

    /**
     * Vérifie si l'adresse principale ne peut pas être supprimée
     */
    private function cannotDeleteMainAddress(Adresse $adresse): bool
    {
        return $adresse->isPrincipale() && $this->getUser()->getAdresses()->count() > 1;
    }
}
