<?php

namespace App\Entity;

use App\Repository\FormationContenuRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FormationContenuRepository::class)]
class FormationContenu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $ecole = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $lieu = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?\DateTime $anneeDebut = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?\DateTime $anneeFin = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $diplome = null;

    #[ORM\ManyToOne(inversedBy: 'FormationContenu')]
    private ?Formation $formation = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEcole(): ?string
    {
        return $this->ecole;
    }

    public function setEcole(?string $ecole): static
    {
        $this->ecole = $ecole;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): static
    {
        $this->lieu = $lieu;

        return $this;
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

    public function getanneeFin(): ?\DateTime
    {
        return $this->anneeFin;
    }

    public function setanneeFin(?\DateTime $anneeFin): static
    {
        $this->anneeFin = $anneeFin;

        return $this;
    }

    public function getDiplome(): ?string
    {
        return $this->diplome;
    }

    public function setDiplome(?string $diplome): static
    {
        $this->diplome = $diplome;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): static
    {
        $this->formation = $formation;

        return $this;
    }

}
