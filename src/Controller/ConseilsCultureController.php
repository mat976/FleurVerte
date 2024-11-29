<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant l'affichage des conseils de culture
 */
#[Route('/conseils-culture')]
class ConseilsCultureController extends AbstractController
{
    /**
     * Affiche la page des conseils de culture
     * 
     * @return Response La page des conseils de culture
     */
    #[Route('', name: 'app_conseils_culture')]
    public function index(): Response
    {
        return $this->render('conseils_culture/index.html.twig', [
            'page_title' => 'Conseils de Culture',
            'meta_description' => 'Découvrez nos conseils pour cultiver vos fleurs dans les meilleures conditions.',
        ]);
    }
}
