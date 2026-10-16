<?php

namespace App\Entity;

use App\Repository\FormationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: FormationRepository::class)]
class Formation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
     #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    /**
     * @var Collection<int, FormationContenu>
     */
    #[ORM\OneToMany(targetEntity: FormationContenu::class, mappedBy: 'formation')]
     #[Groups(["contenuecv:read"])]
    private Collection $FormationContenu;

    public function __construct()
    {
        $this->FormationContenu = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, FormationContenu>
     */
    public function getFormationContenu(): Collection
    {
        return $this->FormationContenu;
    }

    public function addFormationContenu(FormationContenu $formationContenu): static
    {
        if (!$this->FormationContenu->contains($formationContenu)) {
            $this->FormationContenu->add($formationContenu);
            $formationContenu->setFormation($this);
        }

        return $this;
    }

    public function removeFormationContenu(FormationContenu $formationContenu): static
    {
        if ($this->FormationContenu->removeElement($formationContenu)) {
            // set the owning side to null (unless already changed)
            if ($formationContenu->getFormation() === $this) {
                $formationContenu->setFormation(null);
            }
        }

        return $this;
    }
}
