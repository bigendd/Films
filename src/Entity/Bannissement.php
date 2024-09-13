<?php
// src/Entity/Bannissement.php

namespace App\Entity;

use App\Repository\BannissementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BannissementRepository::class)]
class Bannissement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $raison = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateDeBannissement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateFin = null;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $statut = false;

    #[ORM\Column(type: "boolean", options: ["default" => false])]
    private bool $definitif = false;

    #[ORM\OneToOne(inversedBy: 'bannissement', targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Utilisateur $utilisateur = null;

    #[ORM\Column(type: Types::STRING, length: 10,  nullable: true)]
    private ?string $duree = null; // Nouvelle propriété

    public function __construct()
    {
        $this->dateDeBannissement = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): static
    {
        $this->raison = $raison;
        return $this;
    }

    public function getDateDeBannissement(): ?\DateTimeInterface
    {
        return $this->dateDeBannissement;
    }

    public function setDateDeBannissement(\DateTimeInterface $dateDeBannissement): static
    {
        $this->dateDeBannissement = $dateDeBannissement;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTimeInterface $dateFin): static
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function isStatut(): bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function isDefinitif(): bool
    {
        return $this->definitif;
    }

    public function setDefinitif(bool $definitif): static
    {
        $this->definitif = $definitif;
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

    public function getDuree(): ?string 
    {
        return $this->duree;
    }

    public function setDuree(string $duree): static 
    {
        $this->duree = $duree;
        return $this;
    }

    public function getRemainingDays(): ?int 
    {
        if ($this->dateFin) {
            $now = new \DateTime();
            $interval = $now->diff($this->dateFin);
            return $interval->days;
        }
        return null;
    }

    public function isBanned(): bool
    {
        if ($this->definitif) {
            return true;
        }

        if ($this->dateFin !== null && $this->dateFin > new \DateTime()) {
            return true;
        }

        return false;
    }
    public function isBannissementExpired(): bool
    {
        if ($this->dateFin !== null) {
            $now = new \DateTime();
            // Vérifie si la date de fin du bannissement est passée
            return $this->dateFin <= $now;
        }
        return false;
    }
}
