<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImResponsable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImResponsable|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImResponsable|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImResponsable[]    findAll()
 * @method ImResponsable[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImResponsableRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImResponsable::class);
    }

    // /**
    //  * @return ImResponsable[] Returns an array of ImResponsable objects
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
    public function findOneBySomeField($value): ?ImResponsable
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
