<?php

namespace App\Controller;

use App\Entity\Fleur;
use App\Entity\Commentaire;
use App\Form\FleurType;
use App\Form\SearchProductType;
use App\Form\CommentaireType;
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

        // Utiliser la nouvelle méthode de recherche qui inclut les tags
        if (!empty($search) || !empty($tagId)) {
            $options = [
                'sort' => $sort,
                'tag_id' => $tagId
            ];

            $fleurs = $this->fleurRepository->searchByTermOrTag($search, $options);
        } else {
            // Si pas de recherche, récupérer tous les produits avec tri
            $queryBuilder = $this->fleurRepository->createQueryBuilder('f');

            // Appliquer le tri
            switch ($sort) {
                case 'name_desc':
                    $queryBuilder->orderBy('f.nom', 'DESC');
                    break;
                case 'price_asc':
                    $queryBuilder->orderBy('f.prix', 'ASC');
                    break;
                case 'price_desc':
                    $queryBuilder->orderBy('f.prix', 'DESC');
                    break;
                case 'name_asc':
                default:
                    $queryBuilder->orderBy('f.nom', 'ASC');
                    break;
            }

            $fleurs = $queryBuilder->getQuery()->getResult();
        }

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
     * @throws NotFoundHttpException Si le produit n'existe pas
     */
    #[Route('/{id}', name: 'product_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id, Request $request): Response
    {
        $fleur = $this->fleurRepository->find($id);

        if (!$fleur) {
            throw $this->createNotFoundException(self::MESSAGE_NOT_FOUND);
        }

        // Formulaire de commentaire
        $commentaire = new Commentaire();
        $commentaireForm = $this->createForm(CommentaireType::class, $commentaire);
        $commentaireForm->handleRequest($request);

        if ($commentaireForm->isSubmitted() && $commentaireForm->isValid()) {
            if (!$this->getUser()) {
                $this->addFlash('error', 'Vous devez être connecté pour laisser un commentaire.');
                return $this->redirectToRoute('app_login');
            }

            $commentaire->setUser($this->getUser());
            $commentaire->setFleur($fleur);
            $commentaire->setDateCreation(new \DateTime());

            $this->entityManager->persist($commentaire);
            $this->entityManager->flush();

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
    public function deleteCommentaire(int $id, Request $request): Response
    {
        $commentaire = $this->entityManager->getRepository(Commentaire::class)->find($id);

        if (!$commentaire) {
            throw $this->createNotFoundException('Commentaire non trouvé');
        }

        // Vérifier que l'utilisateur est le propriétaire ou admin
        if ($commentaire->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer ce commentaire.');
            return $this->redirectToRoute('product_detail', ['id' => $commentaire->getFleur()->getId()]);
        }

        $fleurId = $commentaire->getFleur()->getId();
        $this->entityManager->remove($commentaire);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé.');
        return $this->redirectToRoute('product_detail', ['id' => $fleurId]);
    }
}
