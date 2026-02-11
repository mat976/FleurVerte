<?php

namespace App\Controller;

use App\Repository\FleurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant la page d'accueil du site
 */
class HomeController extends AbstractController
{
    public function __construct(
        private readonly FleurRepository $fleurRepository
    ) {}

    /**
     * Affiche la page d'accueil
     * 
     * @return Response La page d'accueil du site
     */
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'page_title' => 'Accueil - FleurVerte',
            'meta_description' => 'Bienvenue sur FleurVerte, votre boutique en ligne de fleurs de qualité.',
            'promos' => $this->fleurRepository->findActivePromos(4),
            'latestFleurs' => $this->fleurRepository->findLatest(6),
        ]);
    }
}
