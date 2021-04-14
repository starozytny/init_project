<?php

namespace App\Repository\Immo;

use App\Entity\Immo\ImBien;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ImBien|null find($id, $lockMode = null, $lockVersion = null)
 * @method ImBien|null findOneBy(array $criteria, array $orderBy = null)
 * @method ImBien[]    findAll()
 * @method ImBien[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImBienRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ImBien::class);
    }

    // /**
    //  * @return ImBien[] Returns an array of ImBien objects
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
    public function findOneBySomeField($value): ?ImBien
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
