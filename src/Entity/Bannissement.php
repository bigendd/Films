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

public function getJoursRestant(): ?int 
{
    // Vérifie s'il y a une date de fin définie pour le bannissement
    if ($this->dateFin) {
        // Crée un objet DateTime représentant la date et l'heure actuelles
        $now = new \DateTime();
        // Calcule la différence de temps entre maintenant et la date de fin du bannissement
        $interval = $now->diff($this->dateFin);
        // Retourne le nombre de jours restants jusqu'à la date de fin (sous forme de nombre entier)
        return $interval->days;
    }
    // Si aucune date de fin n'est définie, retourne null
    return null;
}

public function isBanne(): bool
{
    // Si le bannissement est définitif, retourne immédiatement true
    if ($this->definitif) {
        return true;
    }

    // Si une date de fin est définie et qu'elle est dans le futur, retourne true
    if ($this->dateFin !== null && $this->dateFin > new \DateTime()) {
        return true;
    }

    // Si aucune condition de bannissement n'est remplie, retourne false
    return false;
}

public function banneExpiree(): bool
{
    // Vérifie s'il existe une date de fin de bannissement
    if ($this->dateFin !== null) {
        // Crée un objet DateTime représentant la date et l'heure actuelles
        $now = new \DateTime();
        // Compare la date de fin avec la date actuelle pour vérifier si le bannissement est terminé
        return $this->dateFin <= $now;
    }
    // Retourne false si aucune date de fin n'est définie
    return false;
}

}
