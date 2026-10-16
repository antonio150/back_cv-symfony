<?php

namespace App\Entity;

use App\Repository\ExperienceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
class ExperienceContenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?\DateTime $anneeDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?\DateTime $anneeFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $entreprise = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?int $duree = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $typeTravail = null;

    #[ORM\ManyToOne(inversedBy: 'ExperienceContenu')]
    private ?Experience $experience = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?bool $posteActuel = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnneeDebut(): ?\DateTime
    {
        return $this->anneeDebut;
    }

    public function setAnneeDebut(?\DateTime $anneeDebut): static
    {
        $this->anneeDebut = $anneeDebut;

        return $this;
    }

    public function getAnneeFin(): ?\DateTime
    {
        return $this->anneeFin;
    }

    public function setAnneeFin(?\DateTime $anneeFin): static
    {
        $this->anneeFin = $anneeFin;

        return $this;
    }

    public function getEntreprise(): ?string
    {
        return $this->entreprise;
    }

    public function setEntreprise(?string $entreprise): static
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(?string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getTypeTravail(): ?string
    {
        return $this->typeTravail;
    }

    public function setTypeTravail(?string $typeTravail): static
    {
        $this->typeTravail = $typeTravail;

        return $this;
    }

    public function getExperience(): ?Experience
    {
        return $this->experience;
    }

    public function setExperience(?Experience $experience): static
    {
        $this->experience = $experience;

        return $this;
    }

    public function isPosteActuel(): ?bool
    {
        return $this->posteActuel;
    }

    public function setPosteActuel(?bool $posteActuel): static
    {
        $this->posteActuel = $posteActuel;

        return $this;
    }
}
