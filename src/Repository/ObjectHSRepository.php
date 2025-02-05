<?php

namespace App\Repository;

use App\Entity\ObjectHS;
use App\Enum\StatusRequest;
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

    public function findPendingWithLimit(int $limit = 5)
    {
        return $this->createQueryBuilder('o')
            ->where('o.status = :status')
            ->setParameter('status', StatusRequest::PENDING)
            ->orderBy('o.modificationDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les ObjectHS filtrés par statut
     */
    public function findByStatusAndFilterByModificationDate(string $status = 'pending')
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->orderBy('o.modificationDate', 'ASC')
            ->setParameter('status', $status)
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
