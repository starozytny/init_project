<?php

namespace App\Repository\Paiement;

use App\Entity\Paiement\PaLot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PaLot|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaLot|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaLot[]    findAll()
 * @method PaLot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaLotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PaLot::class);
    }

    // /**
    //  * @return PaLot[] Returns an array of PaLot objects
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
    public function findOneBySomeField($value): ?PaLot
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
