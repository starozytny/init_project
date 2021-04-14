<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImFinancial;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImFinancial|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImFinancial|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImFinancial[]    findAll()
 * @method ImFinancial[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImFinancialRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImFinancial::class);
    }

    // /**
    //  * @return ImFinancial[] Returns an array of ImFinancial objects
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
    public function findOneBySomeField($value): ?ImFinancial
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
