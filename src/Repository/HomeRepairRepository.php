<?php

namespace App\Repository;

use App\Entity\HomeRepair;
use App\Enum\StatusRequest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HomeRepair>
 */
class HomeRepairRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HomeRepair::class);
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

    public function findByUser($user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.client = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    public function findPendingWithLimit(int $limit = 5): array
    {
        return array_map(function (HomeRepair $repair) {
            return [
                'repair' => $repair,
                'type' => $repair->getType(), // Ajoute le type de réparation
            ];
        }, $this->createQueryBuilder('h')
            ->where('h.status = :status')
            ->setParameter('status', StatusRequest::PENDING)
            ->orderBy('h.modificationDate', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult());
    }

    public function findByStatusAndFilterByModificationDate(string $status = 'pending'): array
    {
        return array_map(function (HomeRepair $repair) {
            return [
                'repair' => $repair,
                'type' => $repair->getType(), // Ajoute le type de réparation
            ];
        }, $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->orderBy('o.modificationDate', 'ASC')
            ->setParameter('status', $status)
            ->getQuery()
            ->getResult());
    }


    //    /**
    //     * @return HomeRepair[] Returns an array of HomeRepair objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('h.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?HomeRepair
    //    {
    //        return $this->createQueryBuilder('h')
    //            ->andWhere('h.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
