<?php

namespace App\EventSubscriber;

use App\Service\MessageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Environment;

/**
 * Subscriber pour injecter le nombre de messages non lus dans Twig
 */
class MessageNotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security,
        private readonly MessageService $messageService,
        private readonly Environment $twig
    ) {}

    /**
     * Événements auxquels s'abonner
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    /**
     * Méthode exécutée avant chaque contrôleur
     * Injecte le nombre de messages non lus dans Twig
     */
    public function onKernelController(ControllerEvent $event): void
    {
        // Ne s'exécute que pour les requêtes principales, pas les sous-requêtes
        if (!$event->isMainRequest()) {
            return;
        }

        // Vérifie si un utilisateur est connecté
        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        // Compte les messages non lus et les injecte dans Twig
        $unreadCount = $this->messageService->countUnreadMessages($user);
        $this->twig->addGlobal('unread_messages_count', $unreadCount);
    }
}