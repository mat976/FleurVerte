<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Entity\Fleur;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class CartController extends AbstractController
{
    public function __construct(private CartService $cartService)
    {
    }

    #[Route('', name: 'app_cart_index')]
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'cartItems' => $this->cartService->getCartItems(),
            'total' => $this->cartService->getTotal(),
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(Fleur $fleur, Request $request): Response
    {
        $quantity = $request->query->getInt('quantity', 1);
        
        try {
            $this->cartService->addToCart($fleur, $quantity);
            $this->addFlash('success', 'Produit ajouté au panier avec succès.');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(CartItem $cartItem): Response
    {
        if ($cartItem->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $this->cartService->removeFromCart($cartItem);
        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/update/{id}', name: 'app_cart_update', methods: ['POST'])]
    public function update(CartItem $cartItem, Request $request): Response
    {
        if ($cartItem->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $quantity = $request->request->getInt('quantity');
        $this->cartService->updateQuantity($cartItem, $quantity);

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/clear', name: 'app_cart_clear')]
    public function clear(): Response
    {
        $this->cartService->clearCart();
        $this->addFlash('success', 'Panier vidé avec succès.');

        return $this->redirectToRoute('app_cart_index');
    }
}
