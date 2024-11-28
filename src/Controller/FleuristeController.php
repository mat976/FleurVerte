<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\User;
use App\Form\AdresseType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FleuristeController extends AbstractController
{
    #[Route('/fleuriste', name: 'app_fleuriste_index')]
    public function index(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $fleuristes = $userRepository->findFleuristes();
        $user = $this->getUser();

        $adresseForm = null;
        if ($user instanceof User && $user->getFleuriste() && !$user->getFleuriste()->getAdresse()) {
            $adresse = new Adresse();
            $adresseForm = $this->createForm(AdresseType::class, $adresse);
            $adresseForm->handleRequest($request);

            if ($adresseForm->isSubmitted() && $adresseForm->isValid()) {
                $fleuriste = $user->getFleuriste();
                $fleuriste->setAdresse($adresse);
                $entityManager->persist($adresse);
                $entityManager->persist($fleuriste);
                $entityManager->flush();

                $this->addFlash('success', 'Votre adresse a été ajoutée avec succès.');
                return $this->redirectToRoute('app_fleuriste_index');
            }
        }

        return $this->render('fleuriste/index.html.twig', [
            'fleuristes' => $fleuristes,
            'adresseForm' => $adresseForm ? $adresseForm->createView() : null,
        ]);
    }

    #[Route('/fleuriste/{id}', name: 'app_fleuriste_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);

        if (!$user || !$user->getFleuriste()) {
            throw $this->createNotFoundException('Fleuriste non trouvé');
        }

        $fleuriste = $user->getFleuriste();
        $fleurs = $fleuriste->getFleurs();

        return $this->render('fleuriste/detail.html.twig', [
            'fleuriste' => $fleuriste,
            'fleurs' => $fleurs,
        ]);
    }
}
