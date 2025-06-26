<?php

namespace App\EventSubscriber;

use App\Service\CartService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Subscriber pour initialiser le compteur de panier dans la session
 */
class CartSessionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private CartService $cartService,
        private TokenStorageInterface $tokenStorage
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 15], // Priorité plus basse que le firewall
        ];
    }

    /**
     * Initialise le compteur de panier dans la session à chaque requête
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        // Ne pas traiter les sous-requêtes
        if (!$event->isMainRequest()) {
            return;
        }

        // Vérifier si l'utilisateur est connecté
        $token = $this->tokenStorage->getToken();
        if (!$token || !$token->getUser()) {
            return;
        }

        // Initialiser le compteur de panier
        $this->cartService->initCartCount();
    }
}