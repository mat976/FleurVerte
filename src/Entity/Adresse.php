<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant une adresse postale
 * 
 * Cette classe gère les adresses des utilisateurs, permettant de stocker
 * plusieurs adresses par utilisateur avec la possibilité de définir une adresse principale.
 */
#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    /**
     * Identifiant unique de l'adresse
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la rue
     * 
     * @var string|null La rue de l'adresse
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La rue est obligatoire")]
    private ?string $rue = null;

    /**
     * Code postal
     * 
     * @var string|null Le code postal de l'adresse (5 chiffres)
     */
    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    #[Assert\Length(exactly: 5, exactMessage: "Le code postal doit contenir exactement 5 chiffres")]
    private ?string $codePostal = null;

    /**
     * Nom de la ville
     * 
     * @var string|null La ville de l'adresse
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    private ?string $ville = null;

    /**
     * Complément d'adresse optionnel
     * 
     * @var string|null Informations supplémentaires (étage, bâtiment, etc.)
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complement = null;

    /**
     * Utilisateur associé à cette adresse
     * 
     * @var User|null Relation Many-To-One vers l'entité User
     */
    #[ORM\ManyToOne(inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * Indique si cette adresse est l'adresse principale de l'utilisateur
     * 
     * @var bool|null
     */
    #[ORM\Column]
    private ?bool $principale = false;

    /**
     * Récupère l'identifiant de l'adresse
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de la rue
     */
    public function getRue(): ?string
    {
        return $this->rue;
    }

    /**
     * Définit le nom de la rue
     * 
     * @param string $rue La nouvelle rue
     */
    public function setRue(string $rue): static
    {
        $this->rue = $rue;
        return $this;
    }

    /**
     * Récupère le code postal
     */
    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    /**
     * Définit le code postal
     * 
     * @param string $codePostal Le nouveau code postal
     */
    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    /**
     * Récupère le nom de la ville
     */
    public function getVille(): ?string
    {
        return $this->ville;
    }

    /**
     * Définit le nom de la ville
     * 
     * @param string $ville La nouvelle ville
     */
    public function setVille(string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    /**
     * Récupère le complément d'adresse
     */
    public function getComplement(): ?string
    {
        return $this->complement;
    }

    /**
     * Définit le complément d'adresse
     * 
     * @param string|null $complement Le nouveau complément
     */
    public function setComplement(?string $complement): static
    {
        $this->complement = $complement;
        return $this;
    }

    /**
     * Récupère l'utilisateur associé à l'adresse
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Définit l'utilisateur associé à l'adresse
     * 
     * @param User|null $user Le nouvel utilisateur
     */
    public function setUser(?User $user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Vérifie si l'adresse est principale
     */
    public function isPrincipale(): ?bool
    {
        return $this->principale;
    }

    /**
     * Définit si l'adresse est principale
     * 
     * @param bool $principale True si l'adresse est principale
     */
    public function setPrincipale(bool $principale): static
    {
        $this->principale = $principale;
        return $this;
    }

    /**
     * Génère une représentation textuelle complète de l'adresse
     * 
     * @return string L'adresse formatée avec tous ses composants
     */
    public function getAdresseComplete(): string
    {
        $adresse = $this->rue;
        if ($this->complement) {
            $adresse .= ', ' . $this->complement;
        }
        $adresse .= ', ' . $this->codePostal . ' ' . $this->ville;
        return $adresse;
    }
}
