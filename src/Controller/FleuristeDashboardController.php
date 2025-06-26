<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Entity\Fleuriste;
use App\Entity\User;
use App\Form\FleurType;
use App\Form\FleuristeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
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
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur a un profil fleuriste associé
        if ($user->getFleuriste() === null) {
            // Créer un nouveau profil fleuriste pour l'utilisateur
            $fleuriste = new Fleuriste();
            $fleuriste->setUser($user);
            $entityManager->persist($fleuriste);
            $entityManager->flush();
            
            $this->addFlash('info', 'Votre profil fleuriste a été créé. Veuillez compléter vos informations.');
            return $this->redirectToRoute('app_fleuriste_profile_edit');
        }
        
        return $this->render('fleuriste_dashboard/index.html.twig', [
            'fleurs' => $user->getFleuriste()->getFleurs(),
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
        $fleuriste = $user->getFleuriste();
        
        if (!$fleuriste) {
            // Créer une nouvelle entité Fleuriste pour l'utilisateur
            $fleuriste = new Fleuriste();
            $fleuriste->setUser($user);
            $fleuriste->setNom('Mon magasin');
            $fleuriste->setEmail($user->getEmail());
            
            $this->entityManager->persist($fleuriste);
            $this->entityManager->flush();
            
            $this->addFlash('info', 'Votre profil fleuriste a été créé. Veuillez compléter vos informations.');
        }
        
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
        /** @var User $user */
        $user = $this->getUser();
        $fleuriste = $user->getFleuriste();
        
        if (!$fleuriste) {
            // Créer une nouvelle entité Fleuriste pour l'utilisateur
            $fleuriste = new Fleuriste();
            $fleuriste->setUser($user);
            $fleuriste->setNom('Mon magasin');
            $fleuriste->setEmail($user->getEmail());
            
            $this->entityManager->persist($fleuriste);
            $this->entityManager->flush();
            
            $this->addFlash('info', 'Votre profil fleuriste a été créé. Veuillez compléter vos informations.');
        }
        
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
     * @throws AccessDeniedException Si le fleuriste n'est pas propriétaire de la fleur
     */
    #[Route('/fleur/{id}/reapprovisionner', name: 'app_fleuriste_reapprovisionner', methods: ['POST'])]
    public function reapprovisionner(Request $request, Fleur $fleur): Response
    {
        $this->checkFlowerOwnership($fleur);

        $quantite = $request->request->getInt('quantite');
        
        if ($quantite > 0) {
            $fleur->setStock($fleur->getStock() + $quantite);
            $this->entityManager->flush();

            $this->addFlash('success', "La fleur a été réapprovisionnée de {$quantite} unités.");
        }

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }

    /**
     * Vérifie que le fleuriste connecté est bien propriétaire de la fleur
     * 
     * @param Fleur $fleur La fleur à vérifier
     * @throws AccessDeniedException Si le fleuriste n'est pas propriétaire
     */
    private function checkFlowerOwnership(Fleur $fleur): void
    {
        /** @var User $user */
        $user = $this->getUser();
        $currentFleuriste = $user->getFleuriste();
        
        if (!$currentFleuriste) {
            throw $this->createAccessDeniedException('Vous devez avoir un profil fleuriste pour accéder à cette ressource.');
        }
        
        if ($fleur->getFleuriste() !== $currentFleuriste) {
            throw $this->createAccessDeniedException(self::MESSAGE_ACCESS_DENIED);
        }
    }
}
