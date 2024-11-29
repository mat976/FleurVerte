<?php

namespace App\Service;

use App\Entity\CartItem;
use App\Entity\Fleur;
use App\Entity\User;
use App\Repository\CartItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Service de gestion du panier d'achat
 * 
 * Ce service gère toutes les opérations liées au panier d'achat :
 * - Ajout et suppression d'articles
 * - Mise à jour des quantités
 * - Calcul du total
 * - Récupération des articles du panier
 * - Vidage du panier
 */
class CartService
{
    /**
     * Constructeur du service
     * 
     * @param CartItemRepository $cartItemRepository Repository pour les opérations sur CartItem
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités Doctrine
     * @param Security $security Service de sécurité Symfony
     */
    public function __construct(
        private CartItemRepository $cartItemRepository,
        private EntityManagerInterface $entityManager,
        private Security $security
    ) {
    }

    /**
     * Ajoute un produit au panier
     * 
     * Si le produit existe déjà dans le panier, la quantité est augmentée.
     * Sinon, un nouvel élément est créé.
     * 
     * @param Fleur $fleur Le produit à ajouter
     * @param int $quantity La quantité à ajouter (défaut: 1)
     * @throws \RuntimeException Si l'utilisateur n'est pas connecté
     */
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

    /**
     * Supprime un article du panier
     * 
     * @param CartItem $cartItem L'article à supprimer
     */
    public function removeFromCart(CartItem $cartItem): void
    {
        $this->cartItemRepository->remove($cartItem, true);
    }

    /**
     * Met à jour la quantité d'un article
     * 
     * Si la quantité est 0 ou négative, l'article est supprimé du panier.
     * 
     * @param CartItem $cartItem L'article à mettre à jour
     * @param int $quantity La nouvelle quantité
     */
    public function updateQuantity(CartItem $cartItem, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->removeFromCart($cartItem);
            return;
        }

        $cartItem->setQuantity($quantity);
        $this->cartItemRepository->save($cartItem, true);
    }

    /**
     * Récupère tous les articles du panier de l'utilisateur courant
     * 
     * @return CartItem[] Liste des articles du panier
     */
    public function getCartItems(): array
    {
        /** @var User $user */
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        return $this->cartItemRepository->findBy(['user' => $user], ['createdAt' => 'DESC']);
    }

    /**
     * Calcule le montant total du panier
     * 
     * @return float Le montant total du panier
     */
    public function getTotal(): float
    {
        $total = 0;
        foreach ($this->getCartItems() as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }

    /**
     * Vide complètement le panier de l'utilisateur courant
     */
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
