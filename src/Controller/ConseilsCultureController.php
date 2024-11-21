<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConseilsCultureController extends AbstractController
{
    #[Route('/conseils-culture', name: 'app_conseils_culture')]
    public function index(): Response
    {
        return $this->render('conseils_culture/index.html.twig', [
            'controller_name' => 'ConseilsCultureController',
        ]);
    }
}
