<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImDevis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImDevis|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImDevis|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImDevis[]    findAll()
 * @method ImDevis[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImDevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImDevis::class);
    }

    // /**
    //  * @return ImDevis[] Returns an array of ImDevis objects
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
    public function findOneBySomeField($value): ?ImDevis
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
