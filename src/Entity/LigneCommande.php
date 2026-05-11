<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Commande::class, inversedBy: 'lignes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Commande $commande = null;

    #[ORM\ManyToOne(targetEntity: Fleur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Fleur $fleur = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $designation = '';

    #[ORM\Column(type: 'integer')]
    private int $quantite = 1;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $prixUnitaire = '0.00';

    public function getId(): ?int { return $this->id; }

    public function getCommande(): ?Commande { return $this->commande; }
    public function setCommande(?Commande $commande): static { $this->commande = $commande; return $this; }

    public function getFleur(): ?Fleur { return $this->fleur; }
    public function setFleur(?Fleur $fleur): static { $this->fleur = $fleur; return $this; }

    public function getDesignation(): string { return $this->designation; }
    public function setDesignation(string $designation): static { $this->designation = $designation; return $this; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }

    public function getPrixUnitaire(): string { return $this->prixUnitaire; }
    public function setPrixUnitaire(string $prix): static { $this->prixUnitaire = $prix; return $this; }

    public function getSousTotal(): float
    {
        return (float)$this->prixUnitaire * $this->quantite;
    }
}
