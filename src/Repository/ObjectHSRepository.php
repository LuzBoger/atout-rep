<?php

namespace App\Repository;

use App\Entity\ObjectHS;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ObjectHS>
 */
class ObjectHSRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ObjectHS::class);
    }

    public function findByUser($user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.client = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findByUserWithMaximumOfThree($user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.client = :user')
            ->setParameter('user', $user)
            ->setMaxResults(3)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return ObjectHS[] Returns an array of ObjectHS objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ObjectHS
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
