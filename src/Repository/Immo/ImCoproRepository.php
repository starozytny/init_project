<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImCopro;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImCopro|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImCopro|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImCopro[]    findAll()
 * @method ImCopro[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImCoproRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImCopro::class);
    }

    // /**
    //  * @return ImCopro[] Returns an array of ImCopro objects
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
    public function findOneBySomeField($value): ?ImCopro
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
