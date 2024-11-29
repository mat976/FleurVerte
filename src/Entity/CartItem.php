<?php

namespace App\Entity;

use App\Repository\CartItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un élément du panier
 * 
 * Cette classe gère les éléments individuels dans le panier d'un utilisateur,
 * associant une fleur, sa quantité et l'utilisateur propriétaire.
 */
#[ORM\Entity(repositoryClass: CartItemRepository::class)]
class CartItem
{
    /**
     * Identifiant unique de l'élément du panier
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Utilisateur propriétaire de cet élément du panier
     * 
     * @var User|null Relation Many-To-One vers l'entité User
     */
    #[ORM\ManyToOne(inversedBy: 'cartItems')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Fleur associée à cet élément du panier
     * 
     * @var Fleur|null Relation Many-To-One vers l'entité Fleur
     */
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fleur $fleur = null;

    /**
     * Quantité de fleurs dans cet élément du panier
     * 
     * @var int|null
     */
    #[ORM\Column]
    private ?int $quantity = null;

    /**
     * Date de création de l'élément dans le panier
     * 
     * @var \DateTimeImmutable|null
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * Constructeur de l'élément du panier
     * Initialise la date de création et la quantité par défaut à 1
     */
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->quantity = 1;
    }

    /**
     * Récupère l'identifiant de l'élément
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l'utilisateur propriétaire
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur propriétaire
     * 
     * @param User|null $user Le nouvel utilisateur
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Récupère la fleur associée
     */
    public function getFleur(): ?Fleur
    {
        return $this->fleur;
    }

    /**
     * Définit la fleur associée
     * 
     * @param Fleur|null $fleur La nouvelle fleur
     */
    public function setFleur(?Fleur $fleur): self
    {
        $this->fleur = $fleur;
        return $this;
    }

    /**
     * Récupère la quantité
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Définit la quantité
     * 
     * @param int $quantity La nouvelle quantité
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * Récupère la date de création
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Définit la date de création
     * 
     * @param \DateTimeImmutable $createdAt La nouvelle date de création
     */
    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Calcule le total pour cet élément du panier
     * 
     * @return float Le prix total (prix unitaire * quantité)
     */
    public function getTotal(): float
    {
        return $this->fleur->getPrix() * $this->quantity;
    }
}
