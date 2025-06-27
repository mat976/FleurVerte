<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Conversation;
use App\Entity\Fleuriste;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Repository\FleuristeRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Contrôleur pour la gestion des messages et conversations
 */
#[Route('/messages')]
class MessageController extends AbstractController
{
    /**
     * Affiche la liste des conversations de l'utilisateur connecté
     */
    #[Route('/', name: 'app_messages_index', methods: ['GET'])]
    public function index(ConversationRepository $conversationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $conversations = $conversationRepository->findByUser($user);

        return $this->render('message/index.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * Affiche une conversation et permet d'envoyer des messages
     */
    #[Route('/conversation/{id}', name: 'app_conversation_show', methods: ['GET', 'POST'])]
    public function showConversation(
        Request $request,
        Conversation $conversation,
        EntityManagerInterface $entityManager,
        MessageRepository $messageRepository
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Vérifier que l'utilisateur est bien participant à la conversation
        if (($user->isClient() && $conversation->getClient() !== $user->getClient()) ||
            ($user->isFleuriste() && $conversation->getFleuriste() !== $user->getFleuriste())
        ) {
            throw new AccessDeniedException('Vous n\'avez pas accès à cette conversation.');
        }

        // Marquer les messages comme lus
        foreach ($conversation->getMessages() as $message) {
            if ($message->getDestinataire() === $user && !$message->isEstLu()) {
                $message->setEstLu(true);
                $messageRepository->save($message, false);
            }
        }
        $entityManager->flush();

        // Formulaire pour envoyer un nouveau message
        $message = new Message();
        $message->setExpediteur($user);
        $message->setConversation($conversation);

        // Définir le destinataire en fonction de l'expéditeur
        if ($user->isClient()) {
            $message->setDestinataire($conversation->getFleuriste()->getUser());
        } else {
            $message->setDestinataire($conversation->getClient()->getUser());
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $conversation->updateDateDerniereActivite();
            $entityManager->persist($message);
            $entityManager->persist($conversation);
            $entityManager->flush();

            return $this->redirectToRoute('app_conversation_show', ['id' => $conversation->getId()]);
        }

        return $this->render('message/conversation.html.twig', [
            'conversation' => $conversation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Crée une nouvelle conversation avec un fleuriste
     */
    #[Route('/nouveau/{id}', name: 'app_conversation_new', methods: ['GET', 'POST'])]
    public function newConversation(
        Request $request,
        Fleuriste $fleuriste,
        ConversationRepository $conversationRepository,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login', ['redirect' => $request->getUri()]);
        }

        // Vérifier que l'utilisateur n'est pas un fleuriste qui essaie de se contacter lui-même
        if ($user->isFleuriste() && $user->getFleuriste() === $fleuriste) {
            throw new AccessDeniedException('Vous ne pouvez pas vous envoyer un message à vous-même.');
        }

        // Si l'utilisateur n'est pas un client, on doit le rediriger vers la page de modification du profil
        if (!$user->isClient()) {
            $this->addFlash('error', 'Vous devez avoir un profil client pour contacter un fleuriste. Veuillez créer un profil client dans vos paramètres de profil.');
            return $this->redirectToRoute('app_profil_edit');
        }

        $client = $user->getClient();

        // Vérifier si une conversation existe déjà
        $existingConversation = $conversationRepository->findOneByClientAndFleuriste($client, $fleuriste);
        if ($existingConversation) {
            return $this->redirectToRoute('app_conversation_show', ['id' => $existingConversation->getId()]);
        }

        // Créer une nouvelle conversation
        $conversation = new Conversation();
        $conversation->setClient($client);
        $conversation->setFleuriste($fleuriste);
        $conversation->setTitre('Conversation avec ' . $fleuriste->getNom());

        $entityManager->persist($conversation);

        // Créer le premier message
        $message = new Message();
        $message->setExpediteur($user);
        $message->setDestinataire($fleuriste->getUser());
        $message->setConversation($conversation);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_conversation_show', ['id' => $conversation->getId()]);
        }

        return $this->render('message/new_conversation.html.twig', [
            'fleuriste' => $fleuriste,
            'form' => $form->createView(),
        ]);
    }
}
