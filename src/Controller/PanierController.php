<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        // Ici, vous ajouterez plus tard la logique pour récupérer les articles du panier

        return $this->render('panier/index.html.twig', [
            'panier' => [], // Pour l'instant, un panier vide
        ]);
    }
}
