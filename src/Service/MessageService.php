<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service pour la gestion des messages
 */
class MessageService
{
    private EntityManagerInterface $entityManager;
    private MessageRepository $messageRepository;

    /**
     * Constructeur du service
     */
    public function __construct(EntityManagerInterface $entityManager, MessageRepository $messageRepository)
    {
        $this->entityManager = $entityManager;
        $this->messageRepository = $messageRepository;
    }

    /**
     * Compte le nombre de messages non lus pour un utilisateur
     * 
     * @param User $user L'utilisateur concerné
     * @return int Le nombre de messages non lus
     */
    public function countUnreadMessages(User $user): int
    {
        return $this->messageRepository->countUnreadByUser($user);
    }
}