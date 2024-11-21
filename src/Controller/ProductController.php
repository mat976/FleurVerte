<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Form\FleurType;
use App\Repository\FleurRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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

    #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Fleur();
        $form = $this->createForm(FleurType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Le produit a été créé avec succès.');
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/product/{id}', name: 'product_detail')]
    public function detail(FleurRepository $fleurRepository, $id): Response
    {
        $fleur = $fleurRepository->find($id);

        if (!$fleur) {
            throw $this->createNotFoundException('Fleur non trouvée');
        }

        return $this->render('product/detail.html.twig', [
            'fleur' => $fleur,
        ]);
    }
}
