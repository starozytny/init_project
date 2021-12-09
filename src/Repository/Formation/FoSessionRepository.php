<?php

namespace App\Repository\Formation;

use App\Entity\Formation\FoSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FoSession|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoSession|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoSession[]    findAll()
 * @method FoSession[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoSession::class);
    }

    // /**
    //  * @return FoSession[] Returns an array of FoSession objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?FoSession
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
