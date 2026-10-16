<?php

namespace App\Entity;

use App\Repository\LangueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: LangueRepository::class)]
class Langue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    /**
     * @var Collection<int, LangueContenue>
     */
    #[ORM\OneToMany(targetEntity: LangueContenue::class, mappedBy: 'langue')]
    #[Groups(["contenuecv:read"])]
    private Collection $LangueContenue;

    public function __construct()
    {
        $this->LangueContenue = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, LangueContenue>
     */
    public function getLangueContenue(): Collection
    {
        return $this->LangueContenue;
    }

    public function addLangueContenue(LangueContenue $langueContenue): static
    {
        if (!$this->LangueContenue->contains($langueContenue)) {
            $this->LangueContenue->add($langueContenue);
            $langueContenue->setLangue($this);
        }

        return $this;
    }

    public function removeLangueContenue(LangueContenue $langueContenue): static
    {
        if ($this->LangueContenue->removeElement($langueContenue)) {
            // set the owning side to null (unless already changed)
            if ($langueContenue->getLangue() === $this) {
                $langueContenue->setLangue(null);
            }
        }

        return $this;
    }
}
