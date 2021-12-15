<?php

namespace App\Repository\Paiement;

use App\Entity\Paiement\PaOrder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaOrder|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaOrder|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaOrder[]    findAll()
 * @method PaOrder[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaOrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaOrder::class);
    }

    // /**
    //  * @return PaOrder[] Returns an array of PaOrder objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PaOrder
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
