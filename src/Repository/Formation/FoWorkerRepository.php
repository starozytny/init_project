<?php

namespace App\Repository\Formation;

use App\Entity\Formation\FoWorker;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FoWorker|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoWorker|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoWorker[]    findAll()
 * @method FoWorker[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoWorkerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoWorker::class);
    }

    // /**
    //  * @return FoWorker[] Returns an array of FoWorker objects
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
    public function findOneBySomeField($value): ?FoWorker
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
