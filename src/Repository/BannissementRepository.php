<?php

namespace App\Repository;

use App\Entity\Bannissement;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<Bannissement>
 */
class BannissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bannissement::class);
    }

   
    public function findUtilisateursSansBannissementActif(Utilisateur $currentUser): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('utilisateur')
            ->from(Utilisateur::class, 'utilisateur')
            ->leftJoin('utilisateur.bannissement', 'ban')
            ->where('ban.statut = false OR ban.id IS NULL')
            ->andWhere('utilisateur != :currentUser')
            ->setParameter('currentUser', $currentUser);
    }
}
