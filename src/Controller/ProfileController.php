<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Client;
use App\Entity\Fleuriste;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRole = $form->get('role')->getData();
            $validRoles = ['ROLE_USER', 'ROLE_FLEURISTE'];

            // Gestion de l'avatar
            $avatarFile = $form->get('avatarFile')->getData();
            if ($avatarFile) {
                $user->setAvatarFile($avatarFile);
            }

            if (in_array($newRole, $validRoles) && !in_array($newRole, $user->getRoles())) {
                $user->setRoles([$newRole]);

                if ($newRole === 'ROLE_FLEURISTE') {
                    if (!$user->getFleuriste()) {
                        $fleuriste = new Fleuriste();
                        $fleuriste->setUser($user);
                        $fleuriste->setNom($user->getUsername()); // Set a default name
                        $entityManager->persist($fleuriste);
                    }
                    if ($user->getClient()) {
                        $entityManager->remove($user->getClient());
                        $user->setClient(null);
                    }
                } else {
                    if (!$user->getClient()) {
                        $client = new Client();
                        $client->setUser($user);
                        $entityManager->persist($client);
                    }
                    if ($user->getFleuriste()) {
                        $entityManager->remove($user->getFleuriste());
                        $user->setFleuriste(null);
                    }
                }

                $entityManager->flush();
                $this->addFlash('success', 'Votre profil a été mis à jour avec succès. Votre rôle est maintenant ' . ($newRole === 'ROLE_FLEURISTE' ? 'Fleuriste' : 'Client') . '.');
            } else {
                $this->addFlash('info', 'Aucun changement n\'a été effectué sur votre rôle d\'utilisateur.');
            }

            // Persister les changements de l'utilisateur (y compris l'avatar)
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
