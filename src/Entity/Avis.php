<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $filmId = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $commentaire = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDeCreation = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $statut = false;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(length: 150)]
    private ?string $titre = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $archiverPar = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE , nullable: true)]
    private ?\DateTimeInterface $dateArchivage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilmId(): ?int
    {
        return $this->filmId;
    }

    public function setFilmId(int $filmId): static
    {
        $this->filmId = $filmId;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(string $commentaire): static
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    public function getDateDeCreation(): ?\DateTimeInterface
    {
        return $this->dateDeCreation;
    }

    public function setDateDeCreation(\DateTimeInterface $dateDeCreation): static
    {
        $this->dateDeCreation = $dateDeCreation;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): static
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getArchiverPar(): ?string
    {
        return $this->archiverPar;
    }

    public function setArchiverPar(string $archiverPar): static
    {
        $this->archiverPar = $archiverPar;

        return $this;
    }

    public function getDateArchivage(): ?\DateTimeInterface
    {
        return $this->dateArchivage;
    }

    public function setDateArchivage(\DateTimeInterface $dateArchivage): static
    {
        $this->dateArchivage = $dateArchivage;

        return $this;
    }
}
