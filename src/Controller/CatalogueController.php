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

/**
 * Contrôleur gérant le catalogue des fleurs pour les fleuristes
 */
#[IsGranted('ROLE_FLEURISTE')]
class CatalogueController extends AbstractController
{
    private const MESSAGE_FLEUR_EXISTANTE = 'Cette fleur est déjà dans votre catalogue.';
    private const MESSAGE_FLEUR_AJOUTEE = 'La fleur a été ajoutée à votre catalogue.';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FleurRepository $fleurRepository
    ) {}

    /**
     * Affiche le catalogue des fleurs disponibles
     * 
     * @return Response Page du catalogue avec la liste des fleurs
     */
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(): Response
    {
        // Récupération du fleuriste connecté
        $fleuriste = $this->getUser()->getFleuriste();
        
        // Récupération de toutes les fleurs disponibles
        $allFleurs = $this->fleurRepository->findAll();
        
        // Création d'un tableau des noms des fleurs déjà dans la boutique pour optimiser la comparaison
        $fleuristeFleurIds = array_map(
            fn($fleur) => $fleur->getNom(),
            $fleuriste->getFleurs()->toArray()
        );

        return $this->render('catalogue/index.html.twig', [
            'fleurs' => $allFleurs,
            'fleuristeFleurIds' => $fleuristeFleurIds,
        ]);
    }

    /**
     * Ajoute une fleur au catalogue du fleuriste
     * 
     * @param Fleur $fleur La fleur à ajouter au catalogue
     * @return Response Redirection vers le catalogue
     */
    #[Route('/catalogue/add/{id}', name: 'app_catalogue_add')]
    public function addToMarket(Fleur $fleur): Response
    {
        $fleuriste = $this->getUser()->getFleuriste();
        
        // Vérification optimisée si la fleur existe déjà dans le catalogue du fleuriste
        $fleurExistante = $fleuriste->getFleurs()->exists(
            fn($key, $existingFleur) => $existingFleur->getNom() === $fleur->getNom()
        );

        if ($fleurExistante) {
            $this->addFlash('error', self::MESSAGE_FLEUR_EXISTANTE);
            return $this->redirectToRoute('app_catalogue');
        }

        // Création d'une nouvelle instance de fleur pour le catalogue du fleuriste
        $newFleur = (new Fleur())
            ->setNom($fleur->getNom())
            ->setDescription($fleur->getDescription())
            ->setPrix($fleur->getPrix())
            ->setThc($fleur->getThc())
            ->setStock(0)
            ->setFleuriste($fleuriste);

        // Persistance de la nouvelle fleur en base de données
        $this->entityManager->persist($newFleur);
        $this->entityManager->flush();

        $this->addFlash('success', self::MESSAGE_FLEUR_AJOUTEE);
        return $this->redirectToRoute('app_catalogue');
    }
}
