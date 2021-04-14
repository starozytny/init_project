<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImDiagnostic;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImDiagnostic|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImDiagnostic|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImDiagnostic[]    findAll()
 * @method ImDiagnostic[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImDiagnosticRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImDiagnostic::class);
    }

    // /**
    //  * @return ImDiagnostic[] Returns an array of ImDiagnostic objects
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
    public function findOneBySomeField($value): ?ImDiagnostic
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
