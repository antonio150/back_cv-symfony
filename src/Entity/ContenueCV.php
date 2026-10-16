<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\ContenueCV\CreateContenueCVController;
use App\Controller\ContenueCV\DeleteContenueCVController;
use App\Controller\ContenueCV\ListContenueCVController;
use App\Controller\ContenueCV\ShowContenueCVController;
use App\Controller\ContenueCV\UpdateContenueCVController;
use App\Repository\ContenueCVRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: ContenueCVRepository::class)]
#[ApiResource(
    operations: [
       
        new GetCollection(
            uriTemplate: '/contenue/liste_cv',
            controller: ListContenueCVController::class
        ),
        new Get(
            uriTemplate: '/contenue/show_contenue',
            controller: ShowContenueCVController::class
        ),
        
        new Post(
            uriTemplate: '/contenue/create_contenue',
            controller: CreateContenueCVController::class
        ),
       
        new Delete( 
            uriTemplate: '/contenue/delete_contenue/{id}',
            controller: DeleteContenueCVController::class
        ),

        new Post(
            uriTemplate: '/contenue/update_contenue/{id}',
            controller: UpdateContenueCVController::class
        )

        
    ],
)]
class ContenueCV
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["contenuecv:read"])]
    private ?int $id = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Apropos $Apropos = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?AutreActivite $AutreActivite = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Biographie $Biographie = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Competence $Competence = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Experience $Experience = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Formation $Formation = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Langue $Langue = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Groups(["contenuecv:read"])]
    private ?Photo $Photo = null;

    #[ORM\ManyToOne(inversedBy: 'ContenueCV')]
    private ?Utilisateur $utilisateur =null;

    #[ORM\Column(nullable: true)]
    private ?bool $estActif = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApropos(): ?Apropos
    {
        return $this->Apropos;
    }

    public function setApropos(?Apropos $Apropos): static
    {
        $this->Apropos = $Apropos;

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

    public function getBiographie(): ?Biographie
    {
        return $this->Biographie;
    }

    public function setBiographie(?Biographie $Biographie): static
    {
        $this->Biographie = $Biographie;

        return $this;
    }

    public function getCompetence(): ?Competence
    {
        return $this->Competence;
    }

    public function setCompetence(?Competence $Competence): static
    {
        $this->Competence = $Competence;

        return $this;
    }

    public function getExperience(): ?Experience
    {
        return $this->Experience;
    }

    public function setExperience(?Experience $Experience): static
    {
        $this->Experience = $Experience;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->Formation;
    }

    public function setFormation(?Formation $Formation): static
    {
        $this->Formation = $Formation;

        return $this;
    }

    public function getLangue(): ?Langue
    {
        return $this->Langue;
    }

    public function setLangue(?Langue $Langue): static
    {
        $this->Langue = $Langue;

        return $this;
    }

    public function getPhoto(): ?Photo
    {
        return $this->Photo;
    }

    public function setPhoto(?Photo $Photo): static
    {
        $this->Photo = $Photo;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur):static
    {
        $this->utilisateur = $utilisateur;
        return $this;
    }

    public function isEstActif(): ?bool
    {
        return $this->estActif;
    }

    public function setEstActif(?bool $estActif): static
    {
        $this->estActif = $estActif;

        return $this;
    }
}
