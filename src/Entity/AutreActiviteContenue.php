<?php

namespace App\Entity;

use App\Repository\AutreActiviteContenueRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AutreActiviteContenueRepository::class)]
class AutreActiviteContenue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $champ = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $contenue = null;

    #[ORM\ManyToOne(inversedBy: 'autreActiviteContenues')]
    private ?AutreActivite $AutreActivite = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChamp(): ?string
    {
        return $this->champ;
    }

    public function setChamp(?string $champ): static
    {
        $this->champ = $champ;

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

    public function getAutreActivite(): ?AutreActivite
    {
        return $this->AutreActivite;
    }

    public function setAutreActivite(?AutreActivite $AutreActivite): static
    {
        $this->AutreActivite = $AutreActivite;

        return $this;
    }
}
