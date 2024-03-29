<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImSuivi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImSuivi|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImSuivi|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImSuivi[]    findAll()
 * @method ImSuivi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImSuiviRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImSuivi::class);
    }

    // /**
    //  * @return ImSuivi[] Returns an array of ImSuivi objects
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
    public function findOneBySomeField($value): ?ImSuivi
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
