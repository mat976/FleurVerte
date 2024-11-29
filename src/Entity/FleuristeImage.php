<?php

namespace App\Entity;

use App\Repository\FleuristeImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Entité représentant une image associée à un fleuriste
 * 
 * Cette entité gère :
 * - L'upload des images via VichUploader
 * - Le stockage du nom de l'image
 * - Une légende optionnelle pour chaque image
 * - La relation avec le fleuriste propriétaire
 * 
 * @see Fleuriste Pour l'entité parente
 */
#[ORM\Entity(repositoryClass: FleuristeImageRepository::class)]
#[Vich\Uploadable]
class FleuristeImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Nom du fichier image stocké
     */
    #[ORM\Column(length: 255)]
    private ?string $imageName = null;

    /**
     * Fichier image temporaire pour l'upload
     */
    #[Vich\UploadableField(mapping: 'fleuriste_images', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    /**
     * Date de dernière mise à jour
     * Utilisé par VichUploader pour le versioning
     */
    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * Légende optionnelle de l'image
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $caption = null;

    /**
     * Fleuriste propriétaire de l'image
     */
    #[ORM\ManyToOne(inversedBy: 'images')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fleuriste $fleuriste = null;

    public function __construct()
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;
        return $this;
    }

    /**
     * Définit le fichier image
     * Met à jour automatiquement la date de modification
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;
        return $this;
    }

    public function getFleuriste(): ?Fleuriste
    {
        return $this->fleuriste;
    }

    public function setFleuriste(?Fleuriste $fleuriste): self
    {
        $this->fleuriste = $fleuriste;
        return $this;
    }
}
