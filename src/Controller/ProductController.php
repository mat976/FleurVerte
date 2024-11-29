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
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur gérant les opérations CRUD sur les produits (fleurs)
 */
#[Route('/products')]
class ProductController extends AbstractController
{
    private const MESSAGE_CREATED = 'Le produit a été créé avec succès.';
    private const MESSAGE_NOT_FOUND = 'Fleur non trouvée';

    public function __construct(
        private readonly FleurRepository $fleurRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Affiche la liste de tous les produits
     */
    #[Route('', name: 'app_product')]
    public function index(): Response
    {
        return $this->render('product/index.html.twig', [
            'fleurs' => $this->fleurRepository->findAll(),
        ]);
    }

    /**
     * Crée un nouveau produit
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     */
    #[Route('/new', name: 'app_product_new')]
    #[IsGranted('ROLE_FLEURISTE')]
    public function new(Request $request): Response
    {
        $product = new Fleur();
        $form = $this->createForm(FleurType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            $this->addFlash('success', self::MESSAGE_CREATED);
            return $this->redirectToRoute('app_product');
        }

        return $this->render('product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche les détails d'un produit spécifique
     * 
     * @param int $id L'identifiant du produit
     * @throws NotFoundHttpException Si le produit n'existe pas
     */
    #[Route('/{id}', name: 'product_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id): Response
    {
        $fleur = $this->fleurRepository->find($id);

        if (!$fleur) {
            throw $this->createNotFoundException(self::MESSAGE_NOT_FOUND);
        }

        return $this->render('product/detail.html.twig', [
            'fleur' => $fleur,
        ]);
    }
}
