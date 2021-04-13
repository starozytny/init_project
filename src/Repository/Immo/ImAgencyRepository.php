<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImAgency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImAgency|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImAgency|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImAgency[]    findAll()
 * @method ImAgency[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImAgencyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImAgency::class);
    }

    // /**
    //  * @return ImAgency[] Returns an array of ImAgency objects
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
    public function findOneBySomeField($value): ?ImAgency
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
