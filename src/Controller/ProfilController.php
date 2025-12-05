<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Fleuriste;
use App\Entity\User;
use App\Form\ProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Contrôleur gérant le profil utilisateur et ses modifications
 */
#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    #[Route('/', name: 'app_profil_index', methods: ['GET'])]
    public function index(): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        return $this->render('profil/index.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        // Déterminer si l'utilisateur est actuellement fleuriste
        $isCurrentlyFleuriste = $user->getFleuriste() !== null && $user->getFleuriste()->isActif();
        $currentShopName = $user->getFleuriste() ? $user->getFleuriste()->getNom() : '';
        
        $form = $this->createForm(\App\Form\ProfilType::class, $user);
        
        // Pré-remplir le statut fleuriste actuel et le nom de boutique
        $form->get('becomeFleuriste')->setData($isCurrentlyFleuriste ? '1' : '0');
        if ($currentShopName) {
            $form->get('shopName')->setData($currentShopName);
        }
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gestion du statut fleuriste
            $wantToBeFleuriste = $form->get('becomeFleuriste')->getData();
            $shopName = $form->get('shopName')->getData();
            
            if ($wantToBeFleuriste === '1') {
                // Devenir ou rester fleuriste
                if ($user->getFleuriste() === null) {
                    // Vérifier qu'on a un nom de boutique
                    if (empty($shopName)) {
                        $this->addFlash('error', 'Veuillez entrer un nom de boutique pour devenir fleuriste.');
                        return $this->render('profil/edit.html.twig', [
                            'user' => $user,
                            'form' => $form,
                        ]);
                    }
                    
                    // Créer nouveau fleuriste
                    $fleuriste = new Fleuriste();
                    $fleuriste->setUser($user);
                    $fleuriste->setNom($shopName);
                    $fleuriste->setActif(true);
                    $entityManager->persist($fleuriste);
                } else {
                    // Activer et mettre à jour le fleuriste existant
                    $user->getFleuriste()->setActif(true);
                    if ($shopName) {
                        $user->getFleuriste()->setNom($shopName);
                    }
                }
            } else {
                // Redevenir client - désactiver le fleuriste si existant
                if ($user->getFleuriste() !== null) {
                    $user->getFleuriste()->setActif(false);
                }
                
                // Créer un profil client si pas encore existant
                if ($user->getClient() === null) {
                    $client = new Client();
                    $client->setUser($user);
                    $entityManager->persist($client);
                }
            }

            // Gestion de l'avatar
            $avatarFile = $form->get('avatarFile')->getData();
            $avatarName = $form->get('avatarName')->getData();

            if ($avatarFile) {
                $originalFilename = pathinfo($avatarFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$avatarFile->guessExtension();

                try {
                    $avatarFile->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );
                    $user->setAvatarName($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors du téléchargement de l\'avatar.');
                }
            } elseif ($avatarName) {
                $user->setAvatarName($avatarName);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_profil_index');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }
}
