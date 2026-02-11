<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Entity\User;
use App\Form\FleurType;
use App\Form\FleuristeType;
use App\Service\FleuristeService;
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

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FleuristeService $fleuristeService
    ) {}

    /**
     * Affiche le tableau de bord du fleuriste avec la liste de ses produits
     */
    #[Route('/dashboard', name: 'app_fleuriste_dashboard')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $fleuriste = $this->fleuristeService->getOrCreateFleuriste($user);

        if ($user->getFleuriste() === $fleuriste && !$fleuriste->getNom()) {
            $this->addFlash('info', 'Votre profil fleuriste a été créé. Veuillez compléter vos informations.');
            return $this->redirectToRoute('app_fleuriste_profile_edit');
        }

        return $this->render('fleuriste_dashboard/index.html.twig', [
            'fleurs' => $fleuriste->getFleurs(),
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
        /** @var User $user */
        $user = $this->getUser();
        $fleuriste = $this->fleuristeService->getOrCreateFleuriste($user);

        $fleur = (new Fleur())
            ->setFleuriste($fleuriste);

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
     */
    #[Route('/fleur/{id}/edit', name: 'app_fleuriste_fleur_edit')]
    public function edit(Request $request, Fleur $fleur): Response
    {
        $this->fleuristeService->checkFlowerOwnership($fleur, $this->getUser());

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
        /** @var User $user */
        $user = $this->getUser();
        $fleuriste = $this->fleuristeService->getOrCreateFleuriste($user);

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
     * Réapprovisionne une fleur en ajoutant une quantité
     * 
     * @param Request $request La requête HTTP contenant la quantité à ajouter
     * @param Fleur $fleur La fleur à réapprovisionner
     */
    #[Route('/fleur/{id}/reapprovisionner', name: 'app_fleuriste_reapprovisionner', methods: ['POST'])]
    public function reapprovisionner(Request $request, Fleur $fleur): Response
    {
        $this->fleuristeService->checkFlowerOwnership($fleur, $this->getUser());

        $quantite = $request->request->getInt('quantite');
        $this->fleuristeService->reapprovisionner($fleur, $quantite);

        if ($quantite > 0) {
            $this->addFlash('success', "La fleur a été réapprovisionnée de {$quantite} unités.");
        }

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }

    /**
     * Active/désactive la promotion d'une fleur
     */
    #[Route('/fleur/{id}/toggle-promo', name: 'app_fleuriste_toggle_promo', methods: ['POST'])]
    public function togglePromo(Fleur $fleur): Response
    {
        $this->fleuristeService->checkFlowerOwnership($fleur, $this->getUser());

        $fleur->setPromoActive(!$fleur->isPromoActive());
        $this->entityManager->flush();

        $status = $fleur->isPromoActive() ? 'activée' : 'désactivée';
        $this->addFlash('success', "La promotion a été {$status} pour « {$fleur->getNom()} ».");

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }
}
