<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un client
 * 
 * Cette classe gère les informations spécifiques aux clients,
 * établissant une relation one-to-one avec l'entité User.
 */
#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    /**
     * Identifiant unique du client
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Utilisateur associé au client
     * 
     * @var User|null Relation One-To-One vers l'entité User avec cascade
     */
    #[ORM\OneToOne(inversedBy: 'client', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Récupère l'utilisateur associé
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur associé
     * 
     * @param User $user Le nouvel utilisateur
     */
    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Récupère l'identifiant du client
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
