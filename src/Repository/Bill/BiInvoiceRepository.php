<?php

namespace App\Repository\Bill;

use App\Entity\Bill\BiInvoice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BiInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method BiInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method BiInvoice[]    findAll()
 * @method BiInvoice[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiInvoiceRepository extends EntityRepository
{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, BiInvoice::class);
//    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BiInvoice $entity, bool $flush = true): void
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
    public function remove(BiInvoice $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     /**
      * @return BiInvoice[] Returns an array of BiInvoice objects
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

     /**
      * @return BiInvoice[] Returns an array of BiInvoice objects
      */
    public function findWithCustomerBySociety($value): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.society = :val AND b.customerId IS NOT NULL')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
        ;
    }

     /**
      * @return BiInvoice[] Returns an array of BiInvoice objects
      */
    public function findBetweenDates($value, $dateA, $dateB): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.society = :val AND b.dateAt >= :dateA AND b.dateAt <= :dateB')
            ->setParameter('val', $value)
            ->setParameter('dateA', $dateA)
            ->setParameter('dateB', $dateB)
            ->getQuery()
            ->getResult()
        ;
    }

     /**
      * @return BiInvoice[] Returns an array of BiInvoice objects
      */
    public function findBetweenDatesAndStatus($value, $dateA, $dateB, array $status): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.society = :val AND b.status IN (:status) AND b.dateAt >= :dateA AND b.dateAt <= :dateB')
            ->setParameter('val', $value)
            ->setParameter('dateA', $dateA)
            ->setParameter('dateB', $dateB)
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return BiInvoice[] Returns an array of BiInvoice objects
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
    public function findOneBySomeField($value): ?BiInvoice
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
