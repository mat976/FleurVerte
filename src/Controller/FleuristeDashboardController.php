<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Form\FleurType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_FLEURISTE')]
class FleuristeDashboardController extends AbstractController
{
    #[Route('/fleuriste/dashboard', name: 'app_fleuriste_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $fleuriste = $this->getUser()->getFleuriste();
        $fleurs = $fleuriste->getFleurs();

        return $this->render('fleuriste_dashboard/index.html.twig', [
            'fleurs' => $fleurs,
        ]);
    }

    #[Route('/fleuriste/fleur/new', name: 'app_fleuriste_fleur_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fleur = new Fleur();
        $fleur->setFleuriste($this->getUser()->getFleuriste());

        $form = $this->createForm(FleurType::class, $fleur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fleur);
            $entityManager->flush();

            $this->addFlash('success', 'La fleur a été ajoutée avec succès.');
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('fleuriste_dashboard/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/fleuriste/fleur/{id}/edit', name: 'app_fleuriste_fleur_edit')]
    public function edit(Request $request, Fleur $fleur, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que la fleur appartient bien au fleuriste connecté
        if ($fleur->getFleuriste() !== $this->getUser()->getFleuriste()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier cette fleur.');
        }

        $form = $this->createForm(FleurType::class, $fleur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'La fleur a été modifiée avec succès.');
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('fleuriste_dashboard/edit.html.twig', [
            'form' => $form->createView(),
            'fleur' => $fleur,
        ]);
    }
}
