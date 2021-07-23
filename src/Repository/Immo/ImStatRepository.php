<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImStat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImStat|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImStat|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImStat[]    findAll()
 * @method ImStat[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImStatRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImStat::class);
    }

    // /**
    //  * @return ImStat[] Returns an array of ImStat objects
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
    public function findOneBySomeField($value): ?ImStat
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
