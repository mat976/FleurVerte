<?php

namespace App\Controller;

use App\Repository\FleurRepository;
use App\Repository\TagRepository;
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
    public function index(Request $request, FleurRepository $fleurRepository, TagRepository $tagRepository): Response
    {
        $query = $request->query->get('q', '');
        $tagId = $request->query->get('tag', null);
        $sort = $request->query->get('sort', 'name_asc');
        $results = [];
        $selectedTag = null;

        // Récupérer tous les tags pour l'affichage dans le filtre
        $tags = $tagRepository->findAllSortedByName();

        if ($query || $tagId) {
            $options = [
                'sort' => $sort,
                'tag_id' => $tagId
            ];

            // Recherche avancée par nom, description ou tag
            $results = $fleurRepository->searchByTermOrTag($query, $options);

            // Récupérer le tag sélectionné pour l'affichage
            if ($tagId) {
                $selectedTag = $tagRepository->find($tagId);
            }
        }

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'results' => $results,
            'tags' => $tags,
            'selectedTag' => $selectedTag,
            'currentSort' => $sort
        ]);
    }
}
