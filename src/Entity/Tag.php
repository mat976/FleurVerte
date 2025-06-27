<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant un tag pour les fleurs
 * 
 * Cette classe gère les tags qui peuvent être associés aux fleurs
 * pour faciliter leur catégorisation et leur recherche.
 */
#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    /**
     * Identifiant unique du tag
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du tag
     */
    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Le nom du tag ne peut pas être vide")]
    #[Assert\Length(
        max: 50,
        maxMessage: "Le nom du tag ne peut pas dépasser {{ limit }} caractères"
    )]
    private ?string $nom = null;

    /**
     * Couleur du tag (pour l'affichage)
     */
    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "La couleur ne peut pas être vide")]
    #[Assert\Regex(
        pattern: "/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/",
        message: "La couleur doit être au format hexadécimal (ex: #FF5733)"
    )]
    private ?string $couleur = null;

    /**
     * Fleurs associées à ce tag
     * 
     * @var Collection<int, Fleur>
     */
    #[ORM\ManyToMany(targetEntity: Fleur::class, mappedBy: 'tags')]
    private Collection $fleurs;

    public function __construct()
    {
        $this->fleurs = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant du tag
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom du tag
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom du tag
     * 
     * @param string $nom Le nouveau nom
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Récupère la couleur du tag
     */
    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    /**
     * Définit la couleur du tag
     * 
     * @param string $couleur La nouvelle couleur
     */
    public function setCouleur(string $couleur): static
    {
        $this->couleur = $couleur;
        return $this;
    }

    /**
     * Détermine si le texte doit être blanc ou noir en fonction de la couleur de fond
     * Utilise la formule de luminosité perçue: https://www.w3.org/TR/WCAG20-TECHS/G18.html
     */
    public function getTextColor(): string
    {
        // Si pas de couleur définie, retourne noir par défaut
        if (!$this->couleur) {
            return '#000000';
        }

        // Convertir la couleur hexadécimale en RGB
        $hex = ltrim($this->couleur, '#');

        // Gérer les formats courts (#RGB)
        if (strlen($hex) === 3) {
            $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
            $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
            $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        // Calculer la luminosité relative (formule WCAG)
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Si la luminosité est supérieure à 0.5, utiliser du texte noir, sinon blanc
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    /**
     * Récupère les fleurs associées à ce tag
     * 
     * @return Collection<int, Fleur>
     */
    public function getFleurs(): Collection
    {
        return $this->fleurs;
    }

    /**
     * Ajoute une fleur à ce tag
     */
    public function addFleur(Fleur $fleur): static
    {
        if (!$this->fleurs->contains($fleur)) {
            $this->fleurs->add($fleur);
            $fleur->addTag($this);
        }

        return $this;
    }

    /**
     * Retire une fleur de ce tag
     */
    public function removeFleur(Fleur $fleur): static
    {
        if ($this->fleurs->removeElement($fleur)) {
            $fleur->removeTag($this);
        }

        return $this;
    }

    /**
     * Représentation textuelle du tag
     */
    public function __toString(): string
    {
        return $this->nom ?? 'Nouveau tag';
    }
}
