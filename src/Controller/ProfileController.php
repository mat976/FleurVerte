<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Fleuriste;
use App\Entity\User;
use App\Form\UserProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

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
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(UserProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newRole = $form->get('roles')->getData();
            $validRoles = ['ROLE_USER', 'ROLE_FLEURISTE'];

            if (in_array($newRole, $validRoles)) {
                // Gérer le changement de rôle
                if ($newRole === 'ROLE_FLEURISTE') {
                    if (!$user->getFleuriste()) {
                        $fleuriste = new Fleuriste();
                        $fleuriste->setUser($user);
                        $fleuriste->setNom($user->getUsername());
                        $entityManager->persist($fleuriste);
                    }
                } else {
                    // Si l'utilisateur était un fleuriste et devient un client
                    if ($user->getFleuriste()) {
                        // On garde le Fleuriste mais on le désactive
                        $fleuriste = $user->getFleuriste();
                        $fleuriste->setActif(false);
                        $entityManager->persist($fleuriste);
                    }
                }

                $user->setRoles([$newRole]);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a été mis à jour avec succès. Votre rôle est maintenant ' . ($newRole === 'ROLE_FLEURISTE' ? 'Fleuriste' : 'Client') . '.');
            } else {
                $this->addFlash('error', 'Le rôle sélectionné n\'est pas valide.');
                return $this->redirectToRoute('app_profile_edit');
            }

            // ... gestion de l'avatar ...

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
