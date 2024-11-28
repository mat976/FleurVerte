<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Form\AdresseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/adresse')]
class AdresseController extends AbstractController
{
    #[Route('/new', name: 'app_adresse_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adresse = new Adresse();
        $adresse->setUser($this->getUser());
        
        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Si c'est la première adresse, la définir comme principale
            if ($this->getUser()->getAdresses()->isEmpty()) {
                $adresse->setPrincipale(true);
            } elseif ($adresse->isPrincipale()) {
                // Si la nouvelle adresse est principale, on retire le statut principal des autres adresses
                foreach ($this->getUser()->getAdresses() as $existingAdresse) {
                    $existingAdresse->setPrincipale(false);
                }
            }
            
            $entityManager->persist($adresse);
            $entityManager->flush();

            $this->addFlash('success', 'Adresse ajoutée avec succès');
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('adresse/new.html.twig', [
            'adresse' => $adresse,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_adresse_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'adresse appartient bien à l'utilisateur connecté
        if ($adresse->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette adresse');
        }

        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($adresse->isPrincipale()) {
                // Si l'adresse devient principale, on retire le statut principal des autres adresses
                foreach ($this->getUser()->getAdresses() as $existingAdresse) {
                    if ($existingAdresse !== $adresse) {
                        $existingAdresse->setPrincipale(false);
                    }
                }
            }
            
            $entityManager->flush();

            $this->addFlash('success', 'Adresse modifiée avec succès');
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        return $this->render('adresse/edit.html.twig', [
            'adresse' => $adresse,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_adresse_delete', methods: ['POST'])]
    public function delete(Request $request, Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'adresse appartient bien à l'utilisateur connecté
        if ($adresse->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette adresse');
        }

        // Empêcher la suppression de l'adresse principale s'il y a d'autres adresses
        if ($adresse->isPrincipale() && $this->getUser()->getAdresses()->count() > 1) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer l\'adresse principale. Veuillez d\'abord définir une autre adresse comme principale.');
            return $this->redirectToRoute('app_fleuriste_dashboard');
        }

        if ($this->isCsrfTokenValid('delete'.$adresse->getId(), $request->request->get('_token'))) {
            $entityManager->remove($adresse);
            $entityManager->flush();
            $this->addFlash('success', 'Adresse supprimée avec succès');
        }

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }

    #[Route('/{id}/set-principale', name: 'app_adresse_set_principale', methods: ['POST'])]
    public function setPrincipale(Request $request, Adresse $adresse, EntityManagerInterface $entityManager): Response
    {
        // Vérifier que l'adresse appartient bien à l'utilisateur connecté
        if ($adresse->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'avez pas accès à cette adresse');
        }

        if ($this->isCsrfTokenValid('set-principale'.$adresse->getId(), $request->request->get('_token'))) {
            // Retirer le statut principal des autres adresses
            foreach ($this->getUser()->getAdresses() as $existingAdresse) {
                $existingAdresse->setPrincipale($existingAdresse === $adresse);
            }
            
            $entityManager->flush();
            $this->addFlash('success', 'Adresse principale mise à jour avec succès');
        }

        return $this->redirectToRoute('app_fleuriste_dashboard');
    }
}
