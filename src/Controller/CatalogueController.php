<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Repository\FleurRepository;
use App\Service\FleuristeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        private readonly FleurRepository $fleurRepository,
        private readonly FleuristeService $fleuristeService
    ) {}

    /**
     * Affiche le catalogue des fleurs disponibles
     * 
     * @return Response Page du catalogue avec la liste des fleurs
     */
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(): Response
    {
        $fleuriste = $this->getUser()->getFleuriste();
        $allFleurs = $this->fleurRepository->findAll();

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
        $added = $this->fleuristeService->addFleurToCatalogue($fleuriste, $fleur);

        $this->addFlash(
            $added ? 'success' : 'error',
            $added ? self::MESSAGE_FLEUR_AJOUTEE : self::MESSAGE_FLEUR_EXISTANTE
        );

        return $this->redirectToRoute('app_catalogue');
    }
}
