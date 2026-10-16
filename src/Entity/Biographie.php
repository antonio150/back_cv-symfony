<?php

namespace App\Entity;

use App\Repository\BiographieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BiographieRepository::class)]
class Biographie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $email = null;

    /**
     * @var Collection<int, BiographieSuite>
     */
    #[ORM\OneToMany(targetEntity: BiographieSuite::class, mappedBy: 'Biographie')]
    #[Groups(["contenuecv:read"])]
    private Collection $biographieSuites;

    public function __construct()
    {
        $this->biographieSuites = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, BiographieSuite>
     */
    public function getBiographieSuites(): Collection
    {
        return $this->biographieSuites;
    }

    public function addBiographieSuite(BiographieSuite $biographieSuite): static
    {
        if (!$this->biographieSuites->contains($biographieSuite)) {
            $this->biographieSuites->add($biographieSuite);
            $biographieSuite->setBiographie($this);
        }

        return $this;
    }

    public function removeBiographieSuite(BiographieSuite $biographieSuite): static
    {
        if ($this->biographieSuites->removeElement($biographieSuite)) {
            // set the owning side to null (unless already changed)
            if ($biographieSuite->getBiographie() === $this) {
                $biographieSuite->setBiographie(null);
            }
        }

        return $this;
    }
}
