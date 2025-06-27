<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Entité représentant un message entre un client et un fleuriste
 */
#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    /**
     * Identifiant unique du message
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Contenu du message
     */
    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Le contenu du message ne peut pas être vide')]
    private ?string $contenu = null;

    /**
     * Date d'envoi du message
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateEnvoi;

    /**
     * Indique si le message a été lu
     */
    #[ORM\Column(type: 'boolean')]
    private bool $estLu = false;

    /**
     * Utilisateur expéditeur du message
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $expediteur = null;

    /**
     * Utilisateur destinataire du message
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $destinataire = null;

    /**
     * Conversation à laquelle appartient ce message
     */
    #[ORM\ManyToOne(targetEntity: Conversation::class, inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $conversation = null;

    /**
     * Constructeur de l'entité Message
     */
    public function __construct()
    {
        $this->dateEnvoi = new \DateTime();
    }

    /**
     * Récupère l'identifiant du message
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le contenu du message
     */
    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    /**
     * Définit le contenu du message
     */
    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;
        return $this;
    }

    /**
     * Récupère la date d'envoi du message
     */
    public function getDateEnvoi(): \DateTimeInterface
    {
        return $this->dateEnvoi;
    }

    /**
     * Définit la date d'envoi du message
     */
    public function setDateEnvoi(\DateTimeInterface $dateEnvoi): self
    {
        $this->dateEnvoi = $dateEnvoi;
        return $this;
    }

    /**
     * Vérifie si le message a été lu
     */
    public function isEstLu(): bool
    {
        return $this->estLu;
    }

    /**
     * Définit si le message a été lu
     */
    public function setEstLu(bool $estLu): self
    {
        $this->estLu = $estLu;
        return $this;
    }

    /**
     * Récupère l'expéditeur du message
     */
    public function getExpediteur(): ?User
    {
        return $this->expediteur;
    }

    /**
     * Définit l'expéditeur du message
     */
    public function setExpediteur(?User $expediteur): self
    {
        $this->expediteur = $expediteur;
        return $this;
    }

    /**
     * Récupère le destinataire du message
     */
    public function getDestinataire(): ?User
    {
        return $this->destinataire;
    }

    /**
     * Définit le destinataire du message
     */
    public function setDestinataire(?User $destinataire): self
    {
        $this->destinataire = $destinataire;
        return $this;
    }

    /**
     * Récupère la conversation associée
     */
    public function getConversation(): ?Conversation
    {
        return $this->conversation;
    }

    /**
     * Définit la conversation associée
     */
    public function setConversation(?Conversation $conversation): self
    {
        $this->conversation = $conversation;
        return $this;
    }
}