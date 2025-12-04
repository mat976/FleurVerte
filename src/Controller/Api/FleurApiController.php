<?php

namespace App\Controller\Api;

use App\Repository\FleurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class FleurApiController extends AbstractController
{
    #[Route('/fleurs', name: 'api_fleurs_index', methods: ['GET'])]
    public function index(FleurRepository $fleurRepository, Request $request): JsonResponse
    {
        $fleurs = $fleurRepository->findAll();
        
        $data = [];
        $baseUrl = $request->getSchemeAndHttpHost();

        foreach ($fleurs as $fleur) {
            $imageUrl = $fleur->getImageUrl();
            // Ensure we send a full URL if possible, or relative path
            
            $data[] = [
                'id' => $fleur->getId(),
                'nom' => $fleur->getNom(),
                'description' => $fleur->getDescription(),
                'prix' => $fleur->getPrix(),
                'stock_status' => $fleur->getStockStatus(),
                'stock_label' => $fleur->getStockLabel(),
                'stock_color' => $fleur->getStockColor(),
                'thc' => $fleur->getThc(),
                'image_url' => $imageUrl, // App will likely prepend base URL
                'is_pinned' => $fleur->getIsPinned(),
                'fleuriste' => $fleur->getFleuriste() ? $fleur->getFleuriste()->getNom() : null,
            ];
        }

        return $this->json($data);
    }

    #[Route('/fleurs/{id}', name: 'api_fleurs_show', methods: ['GET'])]
    public function show(int $id, FleurRepository $fleurRepository): JsonResponse
    {
        $fleur = $fleurRepository->find($id);

        if (!$fleur) {
            return $this->json(['error' => 'Fleur non trouvée'], 404);
        }

        $data = [
            'id' => $fleur->getId(),
            'nom' => $fleur->getNom(),
            'description' => $fleur->getDescription(),
            'prix' => $fleur->getPrix(),
            'stock_status' => $fleur->getStockStatus(),
            'stock_label' => $fleur->getStockLabel(),
            'stock_color' => $fleur->getStockColor(),
            'thc' => $fleur->getThc(),
            'image_url' => $fleur->getImageUrl(),
            'is_pinned' => $fleur->getIsPinned(),
            'fleuriste' => $fleur->getFleuriste() ? $fleur->getFleuriste()->getNom() : null,
            'tags' => [],
        ];

        foreach ($fleur->getTags() as $tag) {
            $data['tags'][] = [
                'id' => $tag->getId(),
                'nom' => $tag->getNom(),
                'color' => $tag->getCouleur(), // Assuming Tag has color, if not we skip
            ];
        }

        return $this->json($data);
    }
}
