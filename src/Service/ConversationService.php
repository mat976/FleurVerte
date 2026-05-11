<?php

namespace App\Service;

use App\Entity\Client;
use App\Entity\Conversation;
use App\Entity\Fleuriste;
use App\Entity\Message;
use App\Entity\User;
use App\Repository\ConversationRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;

class ConversationService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ConversationRepository $conversationRepository,
        private readonly MessageRepository $messageRepository
    ) {}

    /**
     * Vérifie que l'utilisateur a accès à la conversation
     *
     * @throws \RuntimeException Si accès refusé
     */
    public function checkAccess(User $user, Conversation $conversation): void
    {
        if ($user->isClient() && $conversation->getClient() !== $user->getClient()) {
            throw new \RuntimeException('Vous n\'avez pas accès à cette conversation.');
        }

        if ($user->isFleuriste() && $conversation->getFleuriste() !== $user->getFleuriste()) {
            throw new \RuntimeException('Vous n\'avez pas accès à cette conversation.');
        }
    }

    /**
     * Marque les messages non lus comme lus pour l'utilisateur (requête batch)
     */
    public function markMessagesAsRead(Conversation $conversation, User $user): void
    {
        $this->messageRepository->markAsReadByConversationAndUser($conversation, $user);
    }

    /**
     * Détermine le destinataire d'un message dans une conversation
     */
    public function getRecipient(User $sender, Conversation $conversation): User
    {
        if ($sender->isClient()) {
            return $conversation->getFleuriste()->getUser();
        }

        return $conversation->getClient()->getUser();
    }

    /**
     * Trouve ou crée une conversation entre un client et un fleuriste
     */
    public function findOrCreateConversation(Client $client, Fleuriste $fleuriste): Conversation
    {
        $existing = $this->conversationRepository->findOneByClientAndFleuriste($client, $fleuriste);

        if ($existing) {
            return $existing;
        }

        $conversation = new Conversation();
        $conversation->setClient($client);
        $conversation->setFleuriste($fleuriste);
        $conversation->setTitre('Conversation avec ' . $fleuriste->getNom());

        $this->entityManager->persist($conversation);

        return $conversation;
    }

    /**
     * Crée et persiste un message dans une conversation
     */
    public function createMessage(User $sender, Conversation $conversation, string $contenu): Message
    {
        $message = new Message();
        $message->setExpediteur($sender);
        $message->setConversation($conversation);
        $message->setContenu($contenu);
        $message->setDestinataire($this->getRecipient($sender, $conversation));

        $conversation->updateDateDerniereActivite();
        $this->entityManager->persist($message);
        $this->entityManager->persist($conversation);
        $this->entityManager->flush();

        return $message;
    }

    /**
     * Récupère les nouveaux messages après un ID donné (requête SQL optimisée)
     *
     * @return array<array<string, mixed>>
     */
    public function getNewMessages(Conversation $conversation, User $user, int $lastMessageId): array
    {
        $messages = $this->messageRepository->findNewMessagesByConversationAndUser(
            $conversation,
            $user,
            $lastMessageId
        );

        $result = [];
        foreach ($messages as $message) {
            $result[] = [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi()->format('H:i'),
                'dateEnvoiFull' => $message->getDateEnvoi()->format('d/m/Y H:i'),
                'isOwn' => $message->getExpediteur() === $user,
                'expediteurNom' => $message->getExpediteur()->getUsername(),
            ];
        }

        return $result;
    }
}
