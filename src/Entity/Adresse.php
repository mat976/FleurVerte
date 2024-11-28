<?php

namespace App\Entity;

use App\Repository\AdresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AdresseRepository::class)]
class Adresse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La rue est obligatoire")]
    private ?string $rue = null;

    #[ORM\Column(length: 10)]
    #[Assert\NotBlank(message: "Le code postal est obligatoire")]
    #[Assert\Length(exactly: 5, exactMessage: "Le code postal doit contenir exactement 5 chiffres")]
    private ?string $codePostal = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La ville est obligatoire")]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $complement = null;

    #[ORM\ManyToOne(inversedBy: 'adresses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fleuriste $fleuriste = null;

    #[ORM\Column]
    private ?bool $principale = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): static
    {
        $this->rue = $rue;
        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): static
    {
        $this->codePostal = $codePostal;
        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;
        return $this;
    }

    public function getComplement(): ?string
    {
        return $this->complement;
    }

    public function setComplement(?string $complement): static
    {
        $this->complement = $complement;
        return $this;
    }

    public function getFleuriste(): ?Fleuriste
    {
        return $this->fleuriste;
    }

    public function setFleuriste(?Fleuriste $fleuriste): static
    {
        $this->fleuriste = $fleuriste;
        return $this;
    }

    public function isPrincipale(): ?bool
    {
        return $this->principale;
    }

    public function setPrincipale(bool $principale): static
    {
        $this->principale = $principale;
        return $this;
    }

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
