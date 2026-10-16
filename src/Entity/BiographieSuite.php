<?php

namespace App\Entity;

use App\Repository\BiographieSuiteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: BiographieSuiteRepository::class)]
class BiographieSuite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $titre = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $contenue = null;

    #[ORM\ManyToOne(inversedBy: 'biographieSuites')]
    private ?Biographie $Biographie = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getContenue(): ?string
    {
        return $this->contenue;
    }

    public function setContenue(?string $contenue): static
    {
        $this->contenue = $contenue;

        return $this;
    }

    public function getBiographie(): ?Biographie
    {
        return $this->Biographie;
    }

    public function setBiographie(?Biographie $Biographie): static
    {
        $this->Biographie = $Biographie;

        return $this;
    }
}
