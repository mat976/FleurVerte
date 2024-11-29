<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Entité représentant un utilisateur du système
 * 
 * Cette classe gère les informations des utilisateurs, incluant l'authentification,
 * les rôles, les profils (client/fleuriste) et la gestion des avatars.
 * Implémente les interfaces nécessaires pour la sécurité Symfony.
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[Vich\Uploadable]
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    /**
     * Identifiant unique de l'utilisateur
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * Adresse email de l'utilisateur
     * 
     * @var string|null
     */
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /**
     * Rôles de l'utilisateur dans le système
     * 
     * @var array
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * Mot de passe hashé de l'utilisateur
     * 
     * @var string|null
     */
    #[ORM\Column]
    private ?string $password = null;

    /**
     * Profil fleuriste associé
     * 
     * @var Fleuriste|null Relation One-To-One vers l'entité Fleuriste
     */
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Fleuriste $fleuriste = null;

    /**
     * Profil client associé
     * 
     * @var Client|null Relation One-To-One vers l'entité Client
     */
    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    /**
     * Nom d'utilisateur unique
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    /**
     * Nom du fichier avatar
     * 
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $avatarName = null;

    /**
     * Fichier avatar temporaire pour l'upload
     * 
     * @var File|null
     */
    #[Vich\UploadableField(mapping: 'user_avatars', fileNameProperty: 'avatarName')]
    private ?File $avatarFile = null;

    /**
     * Date de dernière mise à jour
     * 
     * @var \DateTimeInterface|null
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * Collection des adresses de l'utilisateur
     * 
     * @var Collection<int, Adresse>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Adresse::class, orphanRemoval: true)]
    private Collection $adresses;

    /**
     * Constructeur de l'entité User
     * Initialise la collection d'adresses
     */
    public function __construct()
    {
        $this->adresses = new ArrayCollection();
    }

    /**
     * Récupère l'identifiant de l'utilisateur
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Récupère l'email de l'utilisateur
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Définit l'email de l'utilisateur
     * 
     * @param string $email Le nouvel email
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Récupère l'identifiant unique de l'utilisateur (email)
     * 
     * @return string L'email de l'utilisateur
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * Récupère les rôles de l'utilisateur
     * 
     * @return array Liste des rôles
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /**
     * Définit les rôles de l'utilisateur
     * 
     * @param array $roles Les nouveaux rôles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Récupère le mot de passe hashé
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Définit le mot de passe hashé
     * 
     * @param string $password Le nouveau mot de passe hashé
     */
    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Efface les données sensibles de l'utilisateur
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    /**
     * Récupère le profil fleuriste associé
     */
    public function getFleuriste(): ?Fleuriste
    {
        return $this->fleuriste;
    }

    /**
     * Définit le profil fleuriste associé
     * 
     * @param Fleuriste|null $fleuriste Le nouveau profil fleuriste
     */
    public function setFleuriste(?Fleuriste $fleuriste): static
    {
        // unset the owning side of the relation if necessary
        if ($fleuriste === null && $this->fleuriste !== null) {
            $this->fleuriste->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($fleuriste !== null && $fleuriste->getUser() !== $this) {
            $fleuriste->setUser($this);
        }

        $this->fleuriste = $fleuriste;
        return $this;
    }

    /**
     * Récupère le profil client associé
     */
    public function getClient(): ?Client
    {
        return $this->client;
    }

    /**
     * Définit le profil client associé
     * 
     * @param Client|null $client Le nouveau profil client
     */
    public function setClient(?Client $client): static
    {
        // unset the owning side of the relation if necessary
        if ($client === null && $this->client !== null) {
            $this->client->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($client !== null && $client->getUser() !== $this) {
            $client->setUser($this);
        }

        $this->client = $client;
        return $this;
    }

    /**
     * Récupère le nom d'utilisateur
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * Définit le nom d'utilisateur
     * 
     * @param string $username Le nouveau nom d'utilisateur
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Récupère le nom du fichier avatar
     */
    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    /**
     * Définit le nom du fichier avatar
     * 
     * @param string|null $avatarName Le nouveau nom de fichier
     */
    public function setAvatarName(?string $avatarName): static
    {
        $this->avatarName = $avatarName;
        return $this;
    }

    /**
     * Récupère le fichier avatar temporaire
     */
    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    /**
     * Définit le fichier avatar temporaire
     * Met à jour la date de modification
     * 
     * @param File|null $avatarFile Le nouveau fichier
     */
    public function setAvatarFile(?File $avatarFile = null): void
    {
        $this->avatarFile = $avatarFile;

        if ($avatarFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    /**
     * Récupère la date de dernière mise à jour
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Définit la date de dernière mise à jour
     * 
     * @param \DateTimeInterface|null $updatedAt La nouvelle date
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Sérialise les données essentielles de l'utilisateur
     * 
     * @return string
     */
    public function serialize()
    {
        return serialize([
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ]);
    }

    /**
     * Désérialise les données de l'utilisateur
     * 
     * @param string $data Les données sérialisées
     */
    public function unserialize($data)
    {
        [
            $this->id,
            $this->username,
            $this->email,
            $this->password,
        ] = unserialize($data);
    }

    /**
     * Récupère la collection des adresses de l'utilisateur
     * 
     * @return Collection<int, Adresse>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    /**
     * Ajoute une adresse à l'utilisateur
     * 
     * @param Adresse $adresse L'adresse à ajouter
     */
    public function addAdresse(Adresse $adresse): static
    {
        if (!$this->adresses->contains($adresse)) {
            $this->adresses->add($adresse);
            $adresse->setUser($this);
        }

        return $this;
    }

    /**
     * Retire une adresse de l'utilisateur
     * 
     * @param Adresse $adresse L'adresse à retirer
     */
    public function removeAdresse(Adresse $adresse): static
    {
        if ($this->adresses->removeElement($adresse)) {
            // set the owning side to null (unless already changed)
            if ($adresse->getUser() === $this) {
                $adresse->setUser(null);
            }
        }

        return $this;
    }
}
