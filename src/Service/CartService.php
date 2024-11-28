<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Fleur;
use App\Entity\User;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class CartService
{
    public function __construct(
        private CartItemRepository $cartItemRepository,
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    public function addToCart(Fleur $fleur, int $quantity = 1): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            throw new \RuntimeException('Vous devez être connecté pour ajouter des articles au panier.');
        }

        $cartItem = $this->cartItemRepository->findOneBy([
            'user' => $user,
            'fleur' => $fleur
        ]);

        if ($cartItem) {
            $cartItem->setQuantity($cartItem->getQuantity() + $quantity);
        } else {
            $cartItem = new CartItem();
            $cartItem->setUser($user);
            $cartItem->setFleur($fleur);
            $cartItem->setQuantity($quantity);
        }

        $this->cartItemRepository->save($cartItem, true);
    }

    public function removeFromCart(CartItem $cartItem): void
    {
        $this->cartItemRepository->remove($cartItem, true);
    }

    public function updateQuantity(CartItem $cartItem, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($cartItem);
            return;
        }

        $cartItem->setQuantity($quantity);
        $this->cartItemRepository->save($cartItem, true);
    }

    public function getCartItems(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        return $this->cartItemRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCartItems() as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    public function clearCart(): void
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        $cartItems = $this->getCartItems();
        foreach ($cartItems as $item) {
            $this->entityManager->remove($item);
        }
        $this->entityManager->flush();
    }
}
