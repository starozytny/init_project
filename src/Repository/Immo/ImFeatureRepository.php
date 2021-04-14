<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImFeature;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImFeature|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImFeature|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImFeature[]    findAll()
 * @method ImFeature[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImFeatureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImFeature::class);
    }

    // /**
    //  * @return ImFeature[] Returns an array of ImFeature objects
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
    public function findOneBySomeField($value): ?ImFeature
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
