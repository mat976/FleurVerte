<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\User;
use App\Form\AdresseType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur gérant l'affichage et les interactions avec les fleuristes
 */
#[Route('/fleuriste')]
class FleuristeController extends AbstractController
{
    private const MESSAGE_ADDRESS_ADDED = 'Votre adresse a été ajoutée avec succès.';
    private const MESSAGE_NOT_FOUND = 'Fleuriste non trouvé';

    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * Affiche la liste des fleuristes et gère l'ajout d'adresse pour les nouveaux utilisateurs
     * 
     * @param Request $request La requête HTTP
     */
    #[Route('', name: 'app_fleuriste_index')]
    public function index(Request $request): Response
    {
        $fleuristes = $this->userRepository->findFleuristes();
        $adresseForm = $this->handleNewUserAddress($request);

        return $this->render('fleuriste/index.html.twig', [
            'fleuristes' => $fleuristes,
            'adresseForm' => $adresseForm ? $adresseForm->createView() : null,
        ]);
    }

    /**
     * Affiche les détails d'un fleuriste et ses produits
     * 
     * @param int $id L'identifiant du fleuriste
     * @throws NotFoundHttpException Si le fleuriste n'existe pas
     */
    #[Route('/{id}', name: 'app_fleuriste_detail', requirements: ['id' => '\d+'])]
    public function detail(int $id): Response
    {
        $user = $this->userRepository->find($id);

        if (!$user?->getFleuriste()) {
            throw $this->createNotFoundException(self::MESSAGE_NOT_FOUND);
        }

        $fleuriste = $user->getFleuriste();

        return $this->render('fleuriste/detail.html.twig', [
            'fleuriste' => $fleuriste,
            'fleurs' => $fleuriste->getFleurs(),
        ]);
    }

    /**
     * Gère l'ajout d'adresse pour les nouveaux utilisateurs
     * 
     * @param Request $request La requête HTTP
     * @return \Symfony\Component\Form\FormInterface|null Le formulaire d'adresse si nécessaire
     */
    private function handleNewUserAddress(Request $request): ?\Symfony\Component\Form\FormInterface
    {
        $user = $this->getUser();
        if (!$user instanceof User || !$user->getAdresses()->isEmpty()) {
            return null;
        }

        $adresse = (new Adresse())
            ->setPrincipale(true)
            ->setUser($user);

        $form = $this->createForm(AdresseType::class, $adresse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($adresse);
            $this->entityManager->flush();

            $this->addFlash('success', self::MESSAGE_ADDRESS_ADDED);
            return $this->redirectToRoute('app_fleuriste_index');
        }

        return $form;
    }
}
