<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Entity\Fleuriste;
use App\Form\FleurType;
use App\Form\FleuristeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur gérant le tableau de bord des fleuristes
 * Permet la gestion des produits (fleurs) par le fleuriste
 */
#[Route('/fleuriste')]
#[IsGranted('ROLE_FLEURISTE')]
class FleuristeDashboardController extends AbstractController
{
    private const MESSAGE_ADDED = 'La fleur a été ajoutée avec succès.';
    private const MESSAGE_EDITED = 'La fleur a été modifiée avec succès.';
    private const MESSAGE_ACCESS_DENIED = 'Vous n\'êtes pas autorisé à modifier cette fleur.';

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Affiche le tableau de bord du fleuriste avec la liste de ses produits
     */
    #[Route('/dashboard', name: 'app_fleuriste_dashboard')]
    public function index(): Response
    {
        return $this->render('fleuriste_dashboard/index.html.twig', [
            'fleurs' => $this->getUser()->getFleuriste()->getFleurs(),
        ]);
    }

    /**
     * Crée un nouveau produit (fleur)
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     */
    #[Route('/fleur/new', name: 'app_fleuriste_fleur_new')]
    public function new(Request $request): Response
    {
        $fleur = (new Fleur())
            ->setFleuriste($this->getUser()->getFleuriste());

        $form = $this->createForm(FleurType::class, $fleur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($fleur);
            $this->entityManager->flush();

            $this->addFlash('success', self::MESSAGE_ADDED);
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('fleuriste_dashboard/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifie un produit (fleur) existant
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @param Fleur $fleur La fleur à modifier
     * @throws AccessDeniedException Si le fleuriste n'est pas propriétaire de la fleur
     */
    #[Route('/fleur/{id}/edit', name: 'app_fleuriste_fleur_edit')]
    public function edit(Request $request, Fleur $fleur): Response
    {
        $this->checkFlowerOwnership($fleur);

        $form = $this->createForm(FleurType::class, $fleur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();

            $this->addFlash('success', self::MESSAGE_EDITED);
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('fleuriste_dashboard/edit.html.twig', [
            'form' => $form->createView(),
            'fleur' => $fleur,
        ]);
    }

    /**
     * Modifie le profil du fleuriste
     */
    #[Route('/profile/edit', name: 'app_fleuriste_profile_edit')]
    public function editProfile(Request $request): Response
    {
        $fleuriste = $this->getUser()->getFleuriste();
        $form = $this->createForm(FleuristeType::class, $fleuriste);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('fleuriste_dashboard/edit_profile.html.twig', [
            'form' => $form->createView(),
            'fleuriste' => $fleuriste,
        ]);
    }

    /**
     * Vérifie que le fleuriste est bien propriétaire de la fleur
     * 
     * @param Fleur $fleur La fleur à vérifier
     * @throws AccessDeniedException Si le fleuriste n'est pas propriétaire
     */
    private function checkFlowerOwnership(Fleur $fleur): void
    {
        if ($fleur->getFleuriste() !== $this->getUser()->getFleuriste()) {
            throw $this->createAccessDeniedException(self::MESSAGE_ACCESS_DENIED);
        }
    }
}
