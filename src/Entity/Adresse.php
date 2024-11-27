<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private ?int $NumRue = null;

    #[ORM\Column(length: 255)]
    private ?string $NomRue = null;

    #[ORM\Column(type: 'integer')]
    private ?int $CodePost = null;

    #[ORM\OneToOne(mappedBy: 'adresse', targetEntity: Fleuriste::class)]
    private ?Fleuriste $fleuriste = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumRue(): ?int
    {
        return $this->NumRue;
    }

    public function setNumRue(int $NumRue): static
    {
        $this->NumRue = $NumRue;
        return $this;
    }

    public function getNomRue(): ?string
    {
        return $this->NomRue;
    }

    public function setNomRue(string $NomRue): static
    {
        $this->NomRue = $NomRue;
        return $this;
    }

    public function getCodePost(): ?int
    {
        return $this->CodePost;
    }

    public function setCodePost(int $CodePost): static
    {
        $this->CodePost = $CodePost;
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
