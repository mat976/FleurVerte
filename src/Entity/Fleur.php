<?php

namespace App\Entity;

use App\Repository\FleurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Entité représentant une fleur
 * 
 * Cette classe gère les informations relatives aux fleurs disponibles à la vente,
 * incluant leurs caractéristiques, prix, stock et relation avec le fleuriste.
 */
#[ORM\Entity(repositoryClass: FleurRepository::class)]
#[Vich\Uploadable]
class Fleur
{
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }
    /**
     * Identifiant unique de la fleur
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom de la fleur
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    /**
     * Description détaillée de la fleur
     * 
     * @var string|null
     */
    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;


    /**
     * Prix de vente de la fleur
     * 
     * @var float|null
     */
    #[ORM\Column]
    private ?float $prix = null;

    /**
     * Quantité en stock
     * 
     * @var int|null
     */
    #[ORM\Column(nullable: true)]
    private ?int $stock = null;

    /**
     * Indique si la fleur est épinglée (mise en avant)
     * 
     * @var bool
     */
    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $isPinned = false;

    /**
     * Fleuriste vendant cette fleur
     * 
     * @var Fleuriste|null Relation Many-To-One vers l'entité Fleuriste
     */
    #[ORM\ManyToOne(targetEntity: Fleuriste::class, inversedBy: 'fleurs')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fleuriste $fleuriste = null;

    /**
     * Fichier image téléchargé
     */
    #[Vich\UploadableField(mapping: 'fleur_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    /**
     * Nom du fichier image
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imageName = null;

    /**
     * Date de dernière mise à jour
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Tags associés à cette fleur
     * 
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'fleurs')]
    private Collection $tags;

    /**
     * Récupère l'identifiant de la fleur
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le nom de la fleur
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * Définit le nom de la fleur
     * 
     * @param string $nom Le nouveau nom
     */
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    /**
     * Récupère la description de la fleur
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Définit la description de la fleur
     * 
     * @param string|null $description La nouvelle description
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }


    /**
     * Récupère le prix de la fleur
     */
    public function getPrix(): ?float
    {
        return $this->prix;
    }

    /**
     * Définit le prix de la fleur
     * 
     * @param float $prix Le nouveau prix
     */
    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
        return $this;
    }

    /**
     * Récupère la quantité en stock
     */
    public function getStock(): ?int
    {
        return $this->stock;
    }

    /**
     * Définit la quantité en stock
     * 
     * @param int|null $stock La nouvelle quantité
     */
    public function setStock(?int $stock): static
    {
        $this->stock = $stock;
        return $this;
    }

    /**
     * Vérifie si la fleur est épinglée
     */
    public function getIsPinned(): bool
    {
        return $this->isPinned;
    }

    /**
     * Définit si la fleur est épinglée
     * 
     * @param bool $isPinned True si la fleur doit être épinglée
     */
    public function setIsPinned(bool $isPinned): static
    {
        $this->isPinned = $isPinned;
        return $this;
    }

    /**
     * Récupère le fleuriste associé
     */
    public function getFleuriste(): ?Fleuriste
    {
        return $this->fleuriste;
    }

    /**
     * Définit le fleuriste associé
     * 
     * @param Fleuriste|null $fleuriste Le nouveau fleuriste
     */
    public function setFleuriste(?Fleuriste $fleuriste): self
    {
        $this->fleuriste = $fleuriste;
        return $this;
    }

    /**
     * Définit le fichier image
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTime();
        }
    }

    /**
     * Obtient le fichier image
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * Définit le nom de l'image
     */
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    /**
     * Obtient le nom de l'image
     */
    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    /**
     * Obtient l'URL de l'image
     */
    public function getImageUrl(): string
    {
        if ($this->imageName) {
            return '/uploads/fleurs/' . $this->imageName;
        }

        // Image aléatoire par défaut
        $images = ['flower1.jpg', 'flower2.jpg', 'flower3.jpg', 'flower4.jpg', 'flower5.jpg', 'flower6.jpg', 'flower7.jpg', 'flower8.jpg', 'flower9.jpg', 'flower10.jpg', 'flower11.jpg', 'flower12.jpg'];
        return '/img/flowerimg/' . $images[array_rand($images)];
    }

    /**
     * Détermine le statut du stock
     * 
     * @return string Le statut du stock ('out_of_stock', 'low_stock', 'medium_stock', 'in_stock')
     */
    public function getStockStatus(): string
    {
        if ($this->stock === 0) {
            return 'out_of_stock';
        } elseif ($this->stock <= 10) {
            return 'low_stock';
        } elseif ($this->stock <= 50) {
            return 'medium_stock';
        } else {
            return 'in_stock';
        }
    }

    /**
     * Récupère le libellé du statut du stock
     * 
     * @return string Le libellé en français du statut du stock
     */
    public function getStockLabel(): string
    {
        return match ($this->getStockStatus()) {
            'out_of_stock' => 'Rupture de stock',
            'low_stock' => 'Stock faible',
            'medium_stock' => 'Stock moyen',
            'in_stock' => 'En stock',
            default => 'Statut inconnu'
        };
    }

    /**
     * Récupère la couleur associée au statut du stock
     * 
     * @return string La couleur correspondant au statut du stock
     */
    public function getStockColor(): string
    {
        return match ($this->getStockStatus()) {
            'out_of_stock' => 'red',
            'low_stock' => 'orange',
            'medium_stock' => 'yellow',
            'in_stock' => 'green',
            default => 'gray',
        };
    }

    /**
     * Récupère les tags associés à cette fleur
     * 
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    /**
     * Ajoute un tag à cette fleur
     */
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    /**
     * Retire un tag de cette fleur
     */
    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
