<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $pathAbsolute = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["contenuecv:read"])]
    private ?string $pathRelative = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPathAbsolute(): ?string
    {
        return $this->pathAbsolute;
    }

    public function setPathAbsolute(?string $pathAbsolute): static
    {
        $this->pathAbsolute = $pathAbsolute;

        return $this;
    }

    public function getPathRelative(): ?string
    {
        return $this->pathRelative;
    }

    public function setPathRelative(?string $pathRelative): static
    {
        $this->pathRelative = $pathRelative;

        return $this;
    }
}
