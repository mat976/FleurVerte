<?php

namespace App\Entity;

use App\Repository\FleuristeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant un fleuriste
 * 
 * Cette classe gère les informations relatives aux fleuristes,
 * incluant leur profil, leur statut et leur catalogue de fleurs.
 */
#[ORM\Entity(repositoryClass: FleuristeRepository::class)]
class Fleuriste
{
    /**
     * Identifiant unique du fleuriste
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom commercial du fleuriste
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * Utilisateur associé au fleuriste
     * 
     * @var User|null Relation One-To-One vers l'entité User
     */
    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'fleuriste')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * État d'activité du fleuriste
     * 
     * @var bool Indique si le compte du fleuriste est actif
     */
    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    /**
     * Collection des fleurs proposées par le fleuriste
     * 
     * @var Collection<int, Fleur>
     */
    #[ORM\OneToMany(mappedBy: 'fleuriste', targetEntity: Fleur::class, orphanRemoval: true)]
    private Collection $fleurs;

    /**
     * Constructeur de l'entité Fleuriste
     * Initialise la collection de fleurs
     */
    public function __construct()
    {
        $this->fleurs = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant du fleuriste
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom du fleuriste
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom du fleuriste
     * 
     * @param string $nom Le nouveau nom
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

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
     * @param User|null $user Le nouvel utilisateur
     */
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Vérifie si le compte est actif
     */
    public function isActif(): bool
    {
        return $this->actif;
    }

    /**
     * Définit l'état d'activité du compte
     * 
     * @param bool $actif Le nouvel état d'activité
     */
    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * Récupère la collection des fleurs du fleuriste
     * 
     * @return Collection<int, Fleur>
     */
    public function getFleurs(): Collection
    {
        return $this->fleurs;
    }

    /**
     * Ajoute une fleur au catalogue du fleuriste
     * 
     * @param Fleur $fleur La fleur à ajouter
     */
    public function addFleur(Fleur $fleur): self
    {
        if (!$this->fleurs->contains($fleur)) {
            $this->fleurs->add($fleur);
            $fleur->setFleuriste($this);
        }
        return $this;
    }

    /**
     * Retire une fleur du catalogue du fleuriste
     * 
     * @param Fleur $fleur La fleur à retirer
     */
    public function removeFleur(Fleur $fleur): self
    {
        if ($this->fleurs->removeElement($fleur)) {
            // set the owning side to null (unless already changed)
            if ($fleur->getFleuriste() === $this) {
                $fleur->setFleuriste(null);
            }
        }
        return $this;
    }
}
