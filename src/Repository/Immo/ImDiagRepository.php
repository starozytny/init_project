<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImDiag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImDiag|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImDiag|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImDiag[]    findAll()
 * @method ImDiag[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImDiagRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImDiag::class);
    }

    // /**
    //  * @return ImDiag[] Returns an array of ImDiag objects
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
    public function findOneBySomeField($value): ?ImDiag
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
