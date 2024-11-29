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
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom = null;

    /**
     * Description du fleuriste
     * 
     * @var string|null
     */
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /**
     * Adresse du fleuriste
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    /**
     * Numéro de téléphone du fleuriste
     * 
     * @var string|null
     */
    #[ORM\Column(length: 15, nullable: true)]
    private ?string $telephone = null;

    /**
     * Adresse e-mail du fleuriste
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

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
     * Collection des images du fleuriste
     * 
     * @var Collection<int, FleuristeImage>
     */
    #[ORM\OneToMany(mappedBy: 'fleuriste', targetEntity: FleuristeImage::class, cascade: ['persist', 'remove'])]
    private Collection $images;

    /**
     * Constructeur de l'entité Fleuriste
     * Initialise la collection de fleurs et d'images
     */
    public function __construct()
    {
        $this->fleurs = new ArrayCollection();
        $this->images = new ArrayCollection();
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
     * @param string|null $nom Le nouveau nom
     */
    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Récupère la description du fleuriste
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description du fleuriste
     * 
     * @param string|null $description La nouvelle description
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Récupère l'adresse du fleuriste
     */
    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    /**
     * Définit l'adresse du fleuriste
     * 
     * @param string|null $adresse La nouvelle adresse
     */
    public function setAdresse(?string $adresse): self
    {
        $this->adresse = $adresse;
        return $this;
    }

    /**
     * Récupère le numéro de téléphone du fleuriste
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * Définit le numéro de téléphone du fleuriste
     * 
     * @param string|null $telephone Le nouveau numéro de téléphone
     */
    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    /**
     * Récupère l'adresse e-mail du fleuriste
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'adresse e-mail du fleuriste
     * 
     * @param string|null $email La nouvelle adresse e-mail
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
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

    /**
     * @return Collection<int, FleuristeImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(FleuristeImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setFleuriste($this);
        }
        return $this;
    }

    public function removeImage(FleuristeImage $image): self
    {
        if ($this->images->removeElement($image)) {
            if ($image->getFleuriste() === $this) {
                $image->setFleuriste(null);
            }
        }
        return $this;
    }
}
