<?php

namespace App\Repository\Paiement;

use App\Entity\Paiement\PaBank;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaBank|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaBank|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaBank[]    findAll()
 * @method PaBank[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaBankRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaBank::class);
    }

    // /**
    //  * @return PaBank[] Returns an array of PaBank objects
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
    public function findOneBySomeField($value): ?PaBank
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
