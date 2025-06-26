<?php

namespace App\Controller;

use App\Repository\FleurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant les fonctionnalités de recherche
 */
class SearchController extends AbstractController
{
    /**
     * Affiche les résultats de recherche
     */
    #[Route('/search', name: 'app_search')]
    public function index(Request $request, FleurRepository $fleurRepository): Response
    {
        $query = $request->query->get('q', '');
        $results = [];
        
        if ($query) {
            // Recherche simple par nom de fleur
            $results = $fleurRepository->createQueryBuilder('f')
                ->where('f.nom LIKE :query')
                ->setParameter('query', '%' . $query . '%')
                ->getQuery()
                ->getResult();
        }
        
        return $this->render('search/index.html.twig', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}