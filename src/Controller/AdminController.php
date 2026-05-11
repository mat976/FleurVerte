<?php

namespace App\Controller;

use App\Entity\ActionLog;
use App\Entity\Commande;
use App\Entity\Commentaire;
use App\Entity\Fleuriste;
use App\Repository\ActionLogRepository;
use App\Repository\CommandeRepository;
use App\Repository\CommentaireRepository;
use App\Repository\FleuristeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Interface d'administration (réservé ROLE_ADMIN)
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    public function __construct(
        private readonly FleuristeRepository $fleuristeRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    #[Route('', name: 'app_admin_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository,
        CommentaireRepository $commentaireRepository,
        CommandeRepository $commandeRepository
    ): Response {
        $statsCommandes = [
            'en_attente'       => $commandeRepository->countByStatut(Commande::STATUT_EN_ATTENTE),
            'validees'         => $commandeRepository->countByStatut(Commande::STATUT_VALIDEE),
            'expediees'        => $commandeRepository->countByStatut(Commande::STATUT_EXPEDIEE),
            'livrees'          => $commandeRepository->countByStatut(Commande::STATUT_LIVREE),
            'annulees'         => $commandeRepository->countByStatut(Commande::STATUT_ANNULEE),
            'chiffre_affaires' => $commandeRepository->sumTotal(),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'nb_users'        => count($userRepository->findAll()),
            'nb_fleuristes'   => count($this->fleuristeRepository->findByStatut(Fleuriste::STATUT_ACTIF)),
            'nb_attente'      => count($this->fleuristeRepository->findByStatut(Fleuriste::STATUT_EN_ATTENTE)),
            'nb_commentaires' => count($commentaireRepository->findAll()),
            'nb_masques'      => count($commentaireRepository->findBy(['visible' => false])),
            'stats_commandes' => $statsCommandes,
        ]);
    }

    /**
     * Liste les fleuristes en attente de validation + historique
     */
    #[Route('/fleuristes', name: 'app_admin_fleuristes', methods: ['GET'])]
    public function fleuristes(): Response
    {
        return $this->render('admin/fleuristes.html.twig', [
            'en_attente' => $this->fleuristeRepository->findByStatut(Fleuriste::STATUT_EN_ATTENTE),
            'actifs'     => $this->fleuristeRepository->findByStatut(Fleuriste::STATUT_ACTIF),
            'refuses'    => $this->fleuristeRepository->findByStatut(Fleuriste::STATUT_REFUSE),
        ]);
    }

    /**
     * Approuve une demande fleuriste : statut → actif, actif → true
     */
    #[Route('/fleuristes/{id}/approuver', name: 'app_admin_fleuriste_approuver', methods: ['POST'])]
    public function approuver(Fleuriste $fleuriste, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_fleuriste_' . $fleuriste->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_fleuristes');
        }

        $fleuriste->setStatut(Fleuriste::STATUT_ACTIF);
        $fleuriste->setActif(true);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Fleuriste "%s" approuvé avec succès.', $fleuriste->getNom()));
        return $this->redirectToRoute('app_admin_fleuristes');
    }

    /**
     * Refuse une demande fleuriste : statut → refuse, actif → false
     */
    #[Route('/fleuristes/{id}/refuser', name: 'app_admin_fleuriste_refuser', methods: ['POST'])]
    public function refuser(Fleuriste $fleuriste, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_fleuriste_' . $fleuriste->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_fleuristes');
        }

        $fleuriste->setStatut(Fleuriste::STATUT_REFUSE);
        $fleuriste->setActif(false);
        $this->entityManager->flush();

        $this->addFlash('success', sprintf('Fleuriste "%s" refusé.', $fleuriste->getNom()));
        return $this->redirectToRoute('app_admin_fleuristes');
    }

    /**
     * Modération des commentaires
     */
    #[Route('/commentaires', name: 'app_admin_commentaires', methods: ['GET'])]
    public function commentaires(CommentaireRepository $commentaireRepository): Response
    {
        return $this->render('admin/commentaires.html.twig', [
            'visibles' => $commentaireRepository->findBy(['visible' => true], ['dateCreation' => 'DESC']),
            'masques'  => $commentaireRepository->findBy(['visible' => false], ['dateCreation' => 'DESC']),
        ]);
    }

    #[Route('/commentaires/{id}/masquer', name: 'app_admin_commentaire_masquer', methods: ['POST'])]
    public function masquerCommentaire(Commentaire $commentaire, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_commentaire_' . $commentaire->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_commentaires');
        }

        $commentaire->setVisible(false);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commentaire masqué.');
        return $this->redirectToRoute('app_admin_commentaires');
    }

    #[Route('/commentaires/{id}/afficher', name: 'app_admin_commentaire_afficher', methods: ['POST'])]
    public function afficherCommentaire(Commentaire $commentaire, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_commentaire_' . $commentaire->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_commentaires');
        }

        $commentaire->setVisible(true);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commentaire réaffiché.');
        return $this->redirectToRoute('app_admin_commentaires');
    }

    #[Route('/commentaires/{id}/supprimer', name: 'app_admin_commentaire_supprimer', methods: ['POST'])]
    public function supprimerCommentaire(Commentaire $commentaire, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_commentaire_' . $commentaire->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_commentaires');
        }

        $this->entityManager->remove($commentaire);
        $this->entityManager->flush();

        $this->addFlash('success', 'Commentaire supprimé définitivement.');
        return $this->redirectToRoute('app_admin_commentaires');
    }

    /**
     * Gestion des commandes
     */
    #[Route('/commandes', name: 'app_admin_commandes', methods: ['GET'])]
    public function commandes(CommandeRepository $commandeRepository, Request $request): Response
    {
        $filtre = $request->query->get('statut', '');
        $commandes = $filtre
            ? $commandeRepository->findByStatut($filtre)
            : $commandeRepository->findBy([], ['dateCommande' => 'DESC']);

        return $this->render('admin/commandes.html.twig', [
            'commandes'     => $commandes,
            'filtre_statut' => $filtre,
            'statuts'       => [
                Commande::STATUT_EN_ATTENTE,
                Commande::STATUT_VALIDEE,
                Commande::STATUT_EXPEDIEE,
                Commande::STATUT_LIVREE,
                Commande::STATUT_ANNULEE,
            ],
        ]);
    }

    #[Route('/commandes/{id}/statut', name: 'app_admin_commande_statut', methods: ['POST'])]
    public function changerStatutCommande(Commande $commande, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('admin_commande_' . $commande->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_commandes');
        }

        $nouveauStatut = $request->request->get('statut');
        $statuts = [
            Commande::STATUT_EN_ATTENTE,
            Commande::STATUT_VALIDEE,
            Commande::STATUT_EXPEDIEE,
            Commande::STATUT_LIVREE,
            Commande::STATUT_ANNULEE,
        ];

        if (in_array($nouveauStatut, $statuts, true)) {
            $commande->setStatut($nouveauStatut);
            $this->entityManager->flush();
            $this->addFlash('success', 'Statut mis à jour.');
        }

        return $this->redirectToRoute('app_admin_commandes');
    }

    /**
     * Logs système
     */
    #[Route('/logs', name: 'app_admin_logs', methods: ['GET'])]
    public function logs(ActionLogRepository $actionLogRepository): Response
    {
        return $this->render('admin/logs.html.twig', [
            'logs' => $actionLogRepository->findRecent(200),
        ]);
    }
}
