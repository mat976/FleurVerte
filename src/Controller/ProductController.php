<?php

namespace App\Controller;

use App\Entity\Commentaire;
use App\Entity\Fleur;
use App\Form\CommentaireType;
use App\Form\FleurType;
use App\Form\SearchProductType;
use App\Repository\FleurRepository;
use App\Service\CommentaireService;
use App\Service\FleurService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
        private readonly EntityManagerInterface $entityManager,
        private readonly FleurService $fleurService,
        private readonly CommentaireService $commentaireService
    ) {}

    /**
     * Affiche la liste de tous les produits avec options de recherche et tri
     * 
     * @param Request $request La requête HTTP contenant les paramètres de recherche et tri
     */
    #[Route('', name: 'app_product')]
    public function index(Request $request): Response
    {
        $searchForm = $this->createForm(SearchProductType::class, null, [
            'action' => $this->generateUrl('app_product')
        ]);

        $searchForm->handleRequest($request);
        $formData = $searchForm->getData() ?: [];

        $search = $request->query->get('search', $formData['search'] ?? '');
        $sort = $request->query->get('sort', $formData['sort'] ?? 'name_asc');
        $tagId = $request->query->get('tag');

        $fleurs = $this->fleurService->search($search, $sort, $tagId);

        return $this->render('product/index.html.twig', [
            'fleurs' => $fleurs,
            'search' => $search,
            'currentSort' => $sort,
            'searchForm' => $searchForm->createView()
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
     * Affiche les détails d'un produit spécifique avec commentaires
     * 
     * @param int $id L'identifiant du produit
     * @param Request $request La requête HTTP
     */
    #[Route('/{id}', name: 'product_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, Request $request): Response
    {
        $fleur = $this->fleurRepository->find($id);

        if (!$fleur) {
            throw $this->createNotFoundException(self::MESSAGE_NOT_FOUND);
        }

        $commentaire = new Commentaire();
        $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
        $commentaireForm->handleRequest($request);

        if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'Vous devez être connecté pour laisser un commentaire.');
                return $this->redirectToRoute('app_login');
            }

            $this->commentaireService->create($this->getUser(), $fleur, $commentaire);
            $this->addFlash('success', 'Votre commentaire a été ajouté avec succès !');
            return $this->redirectToRoute('product_detail', ['id' => $id]);
        }

        return $this->render('product/detail.html.twig', [
            'fleur' => $fleur,
            'commentaireForm' => $commentaireForm->createView(),
        ]);
    }

    /**
     * Supprime un commentaire
     */
    #[Route('/commentaire/{id}/delete', name: 'commentaire_delete', methods: ['POST'])]
    public function deleteCommentaire(int $id): Response
    {
        $commentaire = $this->entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }

        $fleurId = $commentaire->getFleur()->getId();
        $deleted = $this->commentaireService->delete($commentaire, $this->getUser(), $this->isGranted('ROLE_ADMIN'));

        $this->addFlash(
            $deleted ? 'success' : 'error',
            $deleted ? 'Commentaire supprimé.' : 'Vous ne pouvez pas supprimer ce commentaire.'
        );

        return $this->redirectToRoute('product_detail', ['id' => $fleurId]);
    }
}
