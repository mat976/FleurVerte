<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'authentification et l'inscription des utilisateurs
 */
class SecurityController extends AbstractController
{
    private const DEFAULT_ROLE = 'ROLE_USER';
    private const AVATAR_COUNT = 10;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * Gère la connexion des utilisateurs
     * 
     * @param AuthenticationUtils $authenticationUtils Utilitaire d'authentification Symfony
     */
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Gère la déconnexion des utilisateurs
     * Cette méthode est interceptée par le firewall
     * 
     * @throws \LogicException Cette méthode ne devrait jamais être appelée directement
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException(
            'Cette méthode ne devrait jamais être appelée directement - elle est gérée par le firewall.'
        );
    }

    /**
     * Gère l'inscription des nouveaux utilisateurs
     * 
     * @param Request $request La requête HTTP contenant les données du formulaire
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->configureNewUser($user, $form->get('plainPassword')->getData());
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Configure un nouvel utilisateur avec ses paramètres par défaut
     * 
     * @param User $user L'utilisateur à configurer
     * @param string $plainPassword Le mot de passe en clair à hasher
     */
    private function configureNewUser(User $user, string $plainPassword): void
    {
        $user
            ->setRoles([self::DEFAULT_ROLE])
            ->setPassword($this->passwordHasher->hashPassword($user, $plainPassword))
            ->setAvatarName(sprintf('%d.png', rand(1, self::AVATAR_COUNT)));
    }
}
