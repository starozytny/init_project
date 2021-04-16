<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImDemande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImDemande|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImDemande|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImDemande[]    findAll()
 * @method ImDemande[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImDemandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImDemande::class);
    }

    // /**
    //  * @return ImDemande[] Returns an array of ImDemande objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ImDemande
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
