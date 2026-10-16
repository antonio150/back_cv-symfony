<?php

namespace App\Entity;

use App\Repository\ExperienceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ExperienceRepository::class)]
class Experience
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    /**
     * @var Collection<int, ExperienceContenu>
     */
    #[ORM\OneToMany(targetEntity: ExperienceContenu::class, mappedBy: 'experience')]
    #[Groups(["contenuecv:read"])]
    private Collection $ExperienceContenu;

    public function __construct()
    {
        $this->ExperienceContenu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, ExperienceContenu>
     */
    public function getExperienceContenu(): Collection
    {
        return $this->ExperienceContenu;
    }

    public function addExperienceContenu(ExperienceContenu $experienceContenu): static
    {
        if (!$this->ExperienceContenu->contains($experienceContenu)) {
            $this->ExperienceContenu->add($experienceContenu);
            $experienceContenu->setExperience($this);
        }

        return $this;
    }

    public function removeExperienceContenu(ExperienceContenu $experienceContenu): static
    {
        if ($this->ExperienceContenu->removeElement($experienceContenu)) {
            // set the owning side to null (unless already changed)
            if ($experienceContenu->getExperience() === $this) {
                $experienceContenu->setExperience(null);
            }
        }

        return $this;
    }
}
