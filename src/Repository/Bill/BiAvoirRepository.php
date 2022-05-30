<?php

namespace App\Repository\Bill;

use App\Entity\Bill\BiAvoir;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BiAvoir|null find($id, $lockMode = null, $lockVersion = null)
 * @method BiAvoir|null findOneBy(array $criteria, array $orderBy = null)
 * @method BiAvoir[]    findAll()
 * @method BiAvoir[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiAvoirRepository extends EntityRepository
{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, BiAvoir::class);
//    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BiAvoir $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(BiAvoir $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     /**
      * @return BiAvoir[] Returns an array of BiAvoir objects
      */
    public function findWithContractBySociety($value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.society = :val AND b.contractId IS NOT NULL')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return BiAvoir[] Returns an array of BiAvoir objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BiAvoir
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
