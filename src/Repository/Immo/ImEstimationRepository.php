<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImEstimation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImEstimation|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImEstimation|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImEstimation[]    findAll()
 * @method ImEstimation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImEstimationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImEstimation::class);
    }

    // /**
    //  * @return ImEstimation[] Returns an array of ImEstimation objects
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
    public function findOneBySomeField($value): ?ImEstimation
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
