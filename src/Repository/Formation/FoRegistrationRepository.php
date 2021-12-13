<?php

namespace App\Repository\Formation;

use App\Entity\Formation\FoRegistration;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method FoRegistration|null find($id, $lockMode = null, $lockVersion = null)
 * @method FoRegistration|null findOneBy(array $criteria, array $orderBy = null)
 * @method FoRegistration[]    findAll()
 * @method FoRegistration[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FoRegistrationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FoRegistration::class);
    }

    // /**
    //  * @return FoRegistration[] Returns an array of FoRegistration objects
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
    public function findOneBySomeField($value): ?FoRegistration
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
