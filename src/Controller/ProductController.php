<?php

namespace App\Controller;

use App\Repository\FleurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/products', name: 'app_product')]
    public function index(FleurRepository $fleurRepository): Response
    {
        $fleurs = $fleurRepository->findAll();
        return $this->render('product/index.html.twig', [
            'fleurs' => $fleurs,
        ]);
    }
}
