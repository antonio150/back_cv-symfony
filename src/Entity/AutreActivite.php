<?php

namespace App\Entity;

use App\Repository\AutreActiviteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: AutreActiviteRepository::class)]
class AutreActivite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    /**
     * @var Collection<int, AutreActiviteContenue>
     */
    #[ORM\OneToMany(targetEntity: AutreActiviteContenue::class, mappedBy: 'AutreActivite')]
    #[Groups(["contenuecv:read"])]
    private Collection $autreActiviteContenues;

    public function __construct()
    {
        $this->autreActiviteContenues = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, AutreActiviteContenue>
     */
    public function getAutreActiviteContenues(): Collection
    {
        return $this->autreActiviteContenues;
    }

    public function addAutreActiviteContenue(AutreActiviteContenue $autreActiviteContenue): static
    {
        if (!$this->autreActiviteContenues->contains($autreActiviteContenue)) {
            $this->autreActiviteContenues->add($autreActiviteContenue);
            $autreActiviteContenue->setAutreActivite($this);
        }

        return $this;
    }

    public function removeAutreActiviteContenue(AutreActiviteContenue $autreActiviteContenue): static
    {
        if ($this->autreActiviteContenues->removeElement($autreActiviteContenue)) {
            // set the owning side to null (unless already changed)
            if ($autreActiviteContenue->getAutreActivite() === $this) {
                $autreActiviteContenue->setAutreActivite(null);
            }
        }

        return $this;
    }
}
