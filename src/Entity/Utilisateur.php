<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\Utilisateur\CreateUtilisateurController;
use App\Controller\Utilisateur\DeleteUtilisateurController;
use App\Controller\Utilisateur\ListeUtilisateurController;
use App\Controller\Utilisateur\UpdateUtilisateurController;
use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ApiResource(
    operations: [
       
        new GetCollection(
            uriTemplate: '/utilisateur/liste_utilisateur',
            controller: ListeUtilisateurController::class
        ),
        
        new Post(
            uriTemplate: '/utilisateur/create_utilisateur',
            controller: CreateUtilisateurController::class
        ),
       
        new Delete( 
            uriTemplate: '/utilisateur/delete_utilisateur/{id}',
            controller: DeleteUtilisateurController::class
        ),

        new Patch(
            uriTemplate: '/utilisateur/update_utilisateur/{id}',
            controller: UpdateUtilisateurController::class
        )

        
    ],
)]
class Utilisateur implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["utilisateur:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["utilisateur:read"])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[Groups(["utilisateur:read"])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column( nullable: true)]
    private ?string $password = null;

    /**
     * @var Collection<int, Commentaire>
     */
    #[ORM\OneToMany(targetEntity: ContenueCV::class, mappedBy: 'utilisateur')]
    #[Groups(["utilisateur:read"])]
    private Collection $contenueCV;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["utilisateur:read"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["utilisateur:read"])]
    private ?string $prenom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["utilisateur:read"])]
    private ?\DateTime $dateNaissance = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["utilisateur:read"])]
    private ?bool $sexe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0" . self::class . "\0password"] = hash('crc32c', $this->password);
        
        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    /**
     * @return Collection<int, ContenueCV>
     */
    public function getContenueCV(): Collection
    {
        return $this->contenueCV;
    }

    public function addContenueCV(ContenueCV $contenueCVs): static
    {
        if (!$this->contenueCV->contains($contenueCVs)) {
            $this->contenueCV->add($contenueCVs);
            $contenueCVs->setUtilisateur($this);
        }

        return $this;
    }

    public function removeContenueCV(ContenueCV $contenueCV): static
    {
        if ($this->contenueCV->removeElement($contenueCV)) {
            // set the owning side to null (unless already changed)
            if ($contenueCV->getUtilisateur() === $this) {
                $contenueCV->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTime
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(?\DateTime $dateNaissance): static
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function isSexe(): ?bool
    {
        return $this->sexe;
    }

    public function setSexe(?bool $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }
}
