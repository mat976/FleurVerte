<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    const STATUT_EN_ATTENTE = 'en_attente';
    const STATUT_VALIDEE = 'validee';
    const STATUT_EXPEDIEE = 'expediee';
    const STATUT_LIVREE = 'livree';
    const STATUT_ANNULEE = 'annulee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $statut = self::STATUT_EN_ATTENTE;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $total = '0.00';

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $dateLivraison = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\OneToMany(mappedBy: 'commande', targetEntity: LigneCommande::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $lignes;

    public function __construct()
    {
        $this->dateCommande = new \DateTime();
        $this->lignes = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getTotal(): string { return $this->total; }
    public function setTotal(string $total): static { $this->total = $total; return $this; }

    public function getDateCommande(): ?\DateTimeInterface { return $this->dateCommande; }
    public function setDateCommande(\DateTimeInterface $date): static { $this->dateCommande = $date; return $this; }

    public function getDateLivraison(): ?\DateTimeInterface { return $this->dateLivraison; }
    public function setDateLivraison(?\DateTimeInterface $date): static { $this->dateLivraison = $date; return $this; }

    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }
    public function setAdresseLivraison(?string $adresse): static { $this->adresseLivraison = $adresse; return $this; }

    public function getLignes(): Collection { return $this->lignes; }

    public function addLigne(LigneCommande $ligne): static
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes->add($ligne);
            $ligne->setCommande($this);
        }
        return $this;
    }

    public function removeLigne(LigneCommande $ligne): static
    {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
        }
        return $this;
    }
}
