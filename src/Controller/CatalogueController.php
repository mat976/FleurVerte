<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Repository\FleurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_FLEURISTE')]
class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(FleurRepository $fleurRepository): Response
    {
        $fleuriste = $this->getUser()->getFleuriste();
        
        // Récupérer toutes les fleurs du catalogue
        $allFleurs = $fleurRepository->findAll();
        
        // Créer un tableau des IDs des fleurs déjà dans la boutique du fleuriste
        $fleuristeFleurIds = array_map(
            fn($fleur) => $fleur->getNom(),
            $fleuriste->getFleurs()->toArray()
        );

        return $this->render('catalogue/index.html.twig', [
            'fleurs' => $allFleurs,
            'fleuristeFleurIds' => $fleuristeFleurIds,
        ]);
    }

    #[Route('/catalogue/add/{id}', name: 'app_catalogue_add')]
    public function addToMarket(Fleur $fleur, EntityManagerInterface $entityManager): Response
    {
        $fleuriste = $this->getUser()->getFleuriste();
        
        // Vérifier si la fleur n'est pas déjà dans la boutique
        foreach ($fleuriste->getFleurs() as $existingFleur) {
            if ($existingFleur->getNom() === $fleur->getNom()) {
                $this->addFlash('error', 'Cette fleur est déjà dans votre catalogue.');
                return $this->redirectToRoute('app_catalogue');
            }
        }

        // Créer une nouvelle instance de la fleur pour le fleuriste
        $newFleur = new Fleur();
        $newFleur->setNom($fleur->getNom());
        $newFleur->setDescription($fleur->getDescription());
        $newFleur->setPrix($fleur->getPrix());
        $newFleur->setThc($fleur->getThc());
        $newFleur->setStock(0);
        $newFleur->setFleuriste($fleuriste);

        $entityManager->persist($newFleur);
        $entityManager->flush();

        $this->addFlash('success', 'La fleur a été ajoutée à votre catalogue.');
        return $this->redirectToRoute('app_catalogue');
    }
}
