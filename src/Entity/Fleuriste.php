<?php

namespace App\Entity;

use App\Repository\FleuristeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FleuristeRepository::class)]
class Fleuriste
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToOne(targetEntity: User::class, inversedBy: 'fleuriste')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private bool $actif = true;

    #[ORM\OneToMany(mappedBy: 'fleuriste', targetEntity: Fleur::class, orphanRemoval: true)]
    private Collection $fleurs;

    public function __construct()
    {
        $this->fleurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function isActif(): bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;
        return $this;
    }

    /**
     * @return Collection<int, Fleur>
     */
    public function getFleurs(): Collection
    {
        return $this->fleurs;
    }

    public function addFleur(Fleur $fleur): self
    {
        if (!$this->fleurs->contains($fleur)) {
            $this->fleurs->add($fleur);
            $fleur->setFleuriste($this);
        }
        return $this;
    }

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
