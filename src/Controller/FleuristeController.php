<?php

namespace App\Controller;

use App\Repository\FleuristeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FleuristeController extends AbstractController
{
    #[Route('/fleuriste', name: 'app_fleuriste_index')]
    public function index(FleuristeRepository $fleuristeRepository): Response
    {
        $fleuristes = $fleuristeRepository->findFleuristesWithAddress();

        return $this->render('fleuriste/index.html.twig', [
            'fleuristes' => $fleuristes,
        ]);
    }

    #[Route('/fleuriste/{id}', name: 'app_fleuriste_detail')]
    public function detail(int $id, FleuristeRepository $fleuristeRepository): Response
    {
        $fleuriste = $fleuristeRepository->find($id);

        if (!$fleuriste) {
            throw $this->createNotFoundException('Fleuriste non trouvé');
        }

        return $this->render('fleuriste/detail.html.twig', [
            'fleuriste' => $fleuriste,
        ]);
    }

    // Vous pouvez ajouter d'autres méthodes ici si nécessaire, comme 'app_fleuriste_add'
}
