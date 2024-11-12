<?php
// src/Repository/AvisRepository.php

namespace App\Repository;

use App\Entity\Avis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


class AvisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Avis::class);
    }

    /**
     * @return Avis[] Returns an array of Avis objects with statut false
     */
    public function findByFilmIdAndStatus(int $filmId, bool $statut = false): array
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.filmId = :filmId')
            ->andWhere('a.statut = :statut')
            ->setParameter('filmId', $filmId)
            ->setParameter('statut', $statut)
            ->orderBy('a.dateDeCreation', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
