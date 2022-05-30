<?php

namespace App\Repository\Bill;

use App\Entity\Bill\BiContractCustomer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BiContractCustomer|null find($id, $lockMode = null, $lockVersion = null)
 * @method BiContractCustomer|null findOneBy(array $criteria, array $orderBy = null)
 * @method BiContractCustomer[]    findAll()
 * @method BiContractCustomer[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BiContractCustomerRepository extends EntityRepository
{
//    public function __construct(ManagerRegistry $registry)
//    {
//        parent::__construct($registry, BiContractCustomer::class);
//    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(BiContractCustomer $entity, bool $flush = true): void
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
    public function remove(BiContractCustomer $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

     /**
      * @return BiContractCustomer[] Returns an array of BiContractCustomer objects
      */
    public function findByContractsAndNumeroNotNull(array $contracts): array
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.contract IN (:val) AND b.numero IS NOT NULL')
            ->setParameter('val', $contracts)
            ->orderBy('b.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    // /**
    //  * @return BiContractCustomer[] Returns an array of BiContractCustomer objects
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
    public function findOneBySomeField($value): ?BiContractCustomer
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
