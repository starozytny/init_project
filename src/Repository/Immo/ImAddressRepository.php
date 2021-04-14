<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImAddress;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImAddress[]    findAll()
 * @method ImAddress[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImAddressRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImAddress::class);
    }

    // /**
    //  * @return ImAddress[] Returns an array of ImAddress objects
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
    public function findOneBySomeField($value): ?ImAddress
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
