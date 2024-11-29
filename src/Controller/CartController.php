<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Fleur;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur gérant les opérations liées au panier d'achat
 */
#[Route('/cart')]
class CartController extends AbstractController
{
    // Messages flash constants
    private const MESSAGE_ADDED = 'Produit ajouté au panier avec succès.';
    private const MESSAGE_REMOVED = 'Produit retiré du panier.';
    private const MESSAGE_CLEARED = 'Panier vidé avec succès.';
    private const MESSAGE_ACCESS_DENIED = 'Vous n\'avez pas accès à cet élément du panier.';

    public function __construct(
        private readonly CartService $cartService
    ) {}

    /**
     * Affiche le contenu du panier
     */
    #[Route('', name: 'app_cart_index')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'cartItems' => $this->cartService->getCartItems(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    /**
     * Ajoute un produit au panier
     * 
     * @param Fleur $fleur Le produit à ajouter
     * @param Request $request La requête HTTP contenant la quantité
     */
    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Fleur $fleur, Request $request): Response
    {
        $quantity = max(1, $request->query->getInt('quantity', 1));
        
        try {
            $this->cartService->addToCart($fleur, $quantity);
            $this->addFlash('success', self::MESSAGE_ADDED);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Retire un produit du panier
     * 
     * @param CartItem $cartItem L'élément à retirer du panier
     * @throws AccessDeniedException Si l'utilisateur n'est pas propriétaire de l'élément
     */
    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(CartItem $cartItem): Response
    {
        $this->checkCartItemOwnership($cartItem);

        $this->cartService->removeFromCart($cartItem);
        $this->addFlash('success', self::MESSAGE_REMOVED);

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Met à jour la quantité d'un produit dans le panier
     * 
     * @param CartItem $cartItem L'élément à mettre à jour
     * @param Request $request La requête HTTP contenant la nouvelle quantité
     */
    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(CartItem $cartItem, Request $request): Response
    {
        $this->checkCartItemOwnership($cartItem);

        $quantity = max(1, $request->request->getInt('quantity'));
        $this->cartService->updateQuantity($cartItem, $quantity);

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Vide complètement le panier
     */
    #[Route('/clear', name: 'app_cart_clear')]
    public function clear(): Response
    {
        $this->cartService->clearCart();
        $this->addFlash('success', self::MESSAGE_CLEARED);

        return $this->redirectToRoute('app_cart_index');
    }

    /**
     * Vérifie que l'utilisateur est bien propriétaire de l'élément du panier
     * 
     * @param CartItem $cartItem L'élément à vérifier
     * @throws AccessDeniedException Si l'utilisateur n'est pas propriétaire
     */
    private function checkCartItemOwnership(CartItem $cartItem): void
    {
        if ($cartItem->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException(self::MESSAGE_ACCESS_DENIED);
        }
    }
}
