<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une conversation entre un client et un fleuriste
 */
#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    /**
     * Identifiant unique de la conversation
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Titre de la conversation
     */
    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    /**
     * Date de création de la conversation
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateCreation;

    /**
     * Date de dernière mise à jour de la conversation
     */
    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $dateDerniereActivite;

    /**
     * Client participant à la conversation
     */
    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * Fleuriste participant à la conversation
     */
    #[ORM\ManyToOne(targetEntity: Fleuriste::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Fleuriste $fleuriste = null;

    /**
     * Collection des messages de la conversation
     */
    #[ORM\OneToMany(mappedBy: 'conversation', targetEntity: Message::class, orphanRemoval: true)]
    #[ORM\OrderBy(['dateEnvoi' => 'ASC'])]
    private Collection $messages;

    /**
     * Constructeur de l'entité Conversation
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->dateDerniereActivite = new \DateTime();
    }

    /**
     * Récupère l'identifiant de la conversation
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère le titre de la conversation
     */
    public function getTitre(): ?string
    {
        return $this->titre;
    }

    /**
     * Définit le titre de la conversation
     */
    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    /**
     * Récupère la date de création de la conversation
     */
    public function getDateCreation(): \DateTimeInterface
    {
        return $this->dateCreation;
    }

    /**
     * Définit la date de création de la conversation
     */
    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * Récupère la date de dernière activité de la conversation
     */
    public function getDateDerniereActivite(): \DateTimeInterface
    {
        return $this->dateDerniereActivite;
    }

    /**
     * Définit la date de dernière activité de la conversation
     */
    public function setDateDerniereActivite(\DateTimeInterface $dateDerniereActivite): self
    {
        $this->dateDerniereActivite = $dateDerniereActivite;
        return $this;
    }

    /**
     * Met à jour la date de dernière activité à maintenant
     */
    public function updateDateDerniereActivite(): self
    {
        $this->dateDerniereActivite = new \DateTime();
        return $this;
    }

    /**
     * Récupère le client participant à la conversation
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Définit le client participant à la conversation
     */
    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Récupère le fleuriste participant à la conversation
     */
    public function getFleuriste(): ?Fleuriste
    {
        return $this->fleuriste;
    }

    /**
     * Définit le fleuriste participant à la conversation
     */
    public function setFleuriste(?Fleuriste $fleuriste): self
    {
        $this->fleuriste = $fleuriste;
        return $this;
    }

    /**
     * Récupère la collection des messages de la conversation
     * 
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    /**
     * Ajoute un message à la conversation
     */
    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setConversation($this);
            $this->updateDateDerniereActivite();
        }
        return $this;
    }

    /**
     * Retire un message de la conversation
     */
    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversation() === $this) {
                $message->setConversation(null);
            }
        }
        return $this;
    }

    /**
     * Récupère le dernier message de la conversation
     */
    public function getDernierMessage(): ?Message
    {
        if ($this->messages->isEmpty()) {
            return null;
        }
        
        $criteria = Criteria::create()
            ->orderBy(['dateEnvoi' => Criteria::DESC])
            ->setMaxResults(1);
            
        return $this->messages->matching($criteria)->first() ?: null;
    }

    /**
     * Compte le nombre de messages non lus pour un utilisateur donné
     */
    public function countMessagesNonLus(User $user): int
    {
        $count = 0;
        foreach ($this->messages as $message) {
            if ($message->getDestinataire() === $user && !$message->isEstLu()) {
                $count++;
            }
        }
        return $count;
    }
}