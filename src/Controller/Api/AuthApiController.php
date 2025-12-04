<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class AuthApiController extends AbstractController
{
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $username = $data['username'] ?? null;
        $password = $data['password'] ?? null;

        if (!$username || !$password) {
            return $this->json([
                'success' => false,
                'message' => 'Nom d\'utilisateur et mot de passe requis'
            ], 400);
        }

        // Find user by username
        $user = $userRepository->findOneBy(['username' => $username]);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Identifiants invalides'
            ], 401);
        }

        // Verify password
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'success' => false,
                'message' => 'Identifiants invalides'
            ], 401);
        }

        // Generate a simple token (in production, use JWT or similar)
        $token = bin2hex(random_bytes(32));

        return $this->json([
            'success' => true,
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ],
            'token' => $token
        ]);
    }

    #[Route('/me', name: 'api_me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => 'Non authentifié'
            ], 401);
        }

        return $this->json([
            'success' => true,
            'user' => [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ]
        ]);
    }
}
