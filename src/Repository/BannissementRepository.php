<?php

namespace App\Repository;

use App\Entity\Bannissement;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;


class BannissementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bannissement::class);
    }

   
    public function findUtilisateursSansBannissementActif(Utilisateur $currentUser): QueryBuilder
    {
        return $this->getEntityManager()->createQueryBuilder()  // Création du QueryBuilder
        ->select('utilisateur')  // Sélectionne les utilisateurs
        ->from(Utilisateur::class, 'utilisateur')  // Source de la requête : table des utilisateurs
        ->leftJoin('utilisateur.bannissement', 'ban')  // Jointure gauche avec la table `bannissement`
        ->where('ban.statut = false OR ban.id IS NULL')  // Condition : statut du bannissement est false ou aucune entrée de bannissement
        ->andWhere('utilisateur != :currentUser')  // Exclut l'utilisateur actuel des résultats
        ->setParameter('currentUser', $currentUser);  // Paramètre : l'utilisateur actuel
    }
}
