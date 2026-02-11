<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Fleuriste;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ConversationRepository;
use App\Service\ConversationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function __construct(
        private readonly ConversationService $conversationService,
        private readonly ConversationRepository $conversationRepository
    ) {}

    /**
     * Affiche la liste des conversations de l'utilisateur connecté
     */
    #[Route('/', name: 'app_messages_index', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('message/index.html.twig', [
            'conversations' => $this->conversationRepository->findByUser($user),
        ]);
    }

    /**
     * Affiche une conversation et permet d'envoyer des messages
     */
    #[Route('/conversation/{id}', name: 'app_conversation_show', methods: ['GET', 'POST'])]
    public function showConversation(Request $request, Conversation $conversation): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $this->conversationService->checkAccess($user, $conversation);
        $this->conversationService->markMessagesAsRead($conversation, $user);

        $message = new Message();
        $message->setExpediteur($user);
        $message->setConversation($conversation);
        $message->setDestinataire($this->conversationService->getRecipient($user, $conversation));

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->conversationService->createMessage($user, $conversation, $message->getContenu());
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
    public function newConversation(Request $request, Fleuriste $fleuriste): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login', ['redirect' => $request->getUri()]);
        }

        if ($user->isFleuriste() && $user->getFleuriste() === $fleuriste) {
            throw new AccessDeniedException('Vous ne pouvez pas vous envoyer un message à vous-même.');
        }

        if (!$user->isClient()) {
            $this->addFlash('error', 'Vous devez avoir un profil client pour contacter un fleuriste.');
            return $this->redirectToRoute('app_profil_edit');
        }

        $conversation = $this->conversationService->findOrCreateConversation($user->getClient(), $fleuriste);

        if ($conversation->getId()) {
            return $this->redirectToRoute('app_conversation_show', ['id' => $conversation->getId()]);
        }

        $message = new Message();
        $message->setExpediteur($user);
        $message->setDestinataire($fleuriste->getUser());
        $message->setConversation($conversation);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->conversationService->createMessage($user, $conversation, $message->getContenu());
            return $this->redirectToRoute('app_conversation_show', ['id' => $conversation->getId()]);
        }

        return $this->render('message/new_conversation.html.twig', [
            'fleuriste' => $fleuriste,
            'form' => $form->createView(),
        ]);
    }

    /**
     * API: Récupère les nouveaux messages d'une conversation (pour le live polling)
     */
    #[Route('/api/conversation/{id}/messages', name: 'api_messages_get', methods: ['GET'])]
    public function getMessages(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }

        try {
            $this->conversationService->checkAccess($user, $conversation);
        } catch (\RuntimeException) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $lastMessageId = $request->query->getInt('lastMessageId', 0);
        $messages = $this->conversationService->getNewMessages($conversation, $user, $lastMessageId);

        return new JsonResponse([
            'messages' => $messages,
            'conversationId' => $conversation->getId(),
        ]);
    }

    /**
     * API: Envoie un nouveau message (AJAX)
     */
    #[Route('/api/conversation/{id}/send', name: 'api_message_send', methods: ['POST'])]
    public function sendMessage(Request $request, Conversation $conversation): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non autorisé'], 401);
        }

        try {
            $this->conversationService->checkAccess($user, $conversation);
        } catch (\RuntimeException) {
            return new JsonResponse(['error' => 'Accès refusé'], 403);
        }

        $data = json_decode($request->getContent(), true);
        $contenu = trim($data['contenu'] ?? '');

        if (empty($contenu)) {
            return new JsonResponse(['error' => 'Message vide'], 400);
        }

        $message = $this->conversationService->createMessage($user, $conversation, $contenu);

        return new JsonResponse([
            'success' => true,
            'message' => [
                'id' => $message->getId(),
                'contenu' => $message->getContenu(),
                'dateEnvoi' => $message->getDateEnvoi()->format('H:i'),
                'dateEnvoiFull' => $message->getDateEnvoi()->format('d/m/Y H:i'),
                'isOwn' => true,
                'expediteurNom' => $user->getUsername(),
            ],
        ]);
    }
}
