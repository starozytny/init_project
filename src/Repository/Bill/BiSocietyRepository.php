<?php

namespace App\Repository\Bill;

use App\Entity\Bill\BiSociety;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BiSociety|null find($id, $lockMode = null, $lockVersion = null)
 * @method BiSociety|null findOneBy(array $criteria, array $orderBy = null)
 * @method BiSociety[]    findAll()
 * @method BiSociety[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiSocietyRepository extends EntityRepository
{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, BiSociety::class);
//    }

    // /**
    //  * @return BiSociety[] Returns an array of Society objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BiSociety
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
