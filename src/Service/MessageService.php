<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Service pour la gestion des messages avec cache
 */
class MessageService
{
    private array $localCache = [];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MessageRepository $messageRepository,
        private readonly ?CacheInterface $cache = null
    ) {
    }

    /**
     * Compte le nombre de messages non lus pour un utilisateur (avec cache 60s)
     * 
     * @param User $user L'utilisateur concerné
     * @return int Le nombre de messages non lus
     */
    public function countUnreadMessages(User $user): int
    {
        $userId = $user->getId();
        
        // Cache local par requête (évite les doublons dans une même page)
        if (isset($this->localCache[$userId])) {
            return $this->localCache[$userId];
        }

        // Cache Symfony (60 secondes) pour réduire les requêtes DB
        if ($this->cache) {
            $count = $this->cache->get('unread_messages_' . $userId, function (ItemInterface $item) use ($user) {
                $item->expiresAfter(60);
                return $this->messageRepository->countUnreadByUser($user);
            });
        } else {
            $count = $this->messageRepository->countUnreadByUser($user);
        }

        $this->localCache[$userId] = $count;
        return $count;
    }

    /**
     * Invalide le cache du compteur pour un utilisateur
     */
    public function invalidateUnreadCountCache(User $user): void
    {
        $userId = $user->getId();
        unset($this->localCache[$userId]);
        
        if ($this->cache) {
            $this->cache->delete('unread_messages_' . $userId);
        }
    }
}