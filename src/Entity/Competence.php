<?php

namespace App\Entity;

use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]
class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    /**
     * @var Collection<int, CompetenceContenu>
     */
    #[ORM\OneToMany(targetEntity: CompetenceContenu::class, mappedBy: 'Competences')]
    #[Groups(["contenuecv:read"])]
    private Collection $competenceContenus;

    public function __construct()
    {
        $this->competenceContenus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, CompetenceContenu>
     */
    public function getCompetenceContenus(): Collection
    {
        return $this->competenceContenus;
    }

    public function addCompetenceContenu(CompetenceContenu $competenceContenu): static
    {
        if (!$this->competenceContenus->contains($competenceContenu)) {
            $this->competenceContenus->add($competenceContenu);
            $competenceContenu->setCompetences($this);
        }

        return $this;
    }

    public function removeCompetenceContenu(CompetenceContenu $competenceContenu): static
    {
        if ($this->competenceContenus->removeElement($competenceContenu)) {
            // set the owning side to null (unless already changed)
            if ($competenceContenu->getCompetences() === $this) {
                $competenceContenu->setCompetences(null);
            }
        }

        return $this;
    }
}
