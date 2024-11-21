<?php

namespace App\Controller;

use App\Entity\Fleur;
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

    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(Fleur $fleur): Response
    {
        return $this->render('product/detail.html.twig', [
            'fleur' => $fleur,
        ]);
    }
}
