<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfilType;
use App\Service\ProfilService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Contrôleur gérant le profil utilisateur et ses modifications
 */
#[Route('/profil')]
#[IsGranted('ROLE_USER')]
class ProfilController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ProfilService $profilService
    ) {}

    #[Route('/', name: 'app_profil_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    #[Route('/edit', name: 'app_profil_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $isCurrentlyFleuriste = $user->getFleuriste() !== null && $user->getFleuriste()->isActif();
        $currentShopName = $user->getFleuriste() ? $user->getFleuriste()->getNom() : '';

        $form = $this->createForm(ProfilType::class, $user);
        $form->get('becomeFleuriste')->setData($isCurrentlyFleuriste ? '1' : '0');
        if ($currentShopName) {
            $form->get('shopName')->setData($currentShopName);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $error = $this->profilService->handleRoleToggle(
                $user,
                $form->get('becomeFleuriste')->getData(),
                $form->get('shopName')->getData()
            );

            if ($error) {
                $this->addFlash('error', $error);
                return $this->render('profil/edit.html.twig', [
                    'user' => $user,
                    'form' => $form,
                ]);
            }

            $avatarError = $this->profilService->handleAvatar(
                $user,
                $form->get('avatarFile')->getData(),
                $form->get('avatarName')->getData()
            );

            if ($avatarError) {
                $this->addFlash('error', $avatarError);
            }

            $this->entityManager->flush();

            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_profil_index');
        }

        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/delete', name: 'app_profil_delete', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $this->entityManager->remove($user);
            $this->entityManager->flush();
            $this->addFlash('success', 'Votre compte a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_home');
    }
}
