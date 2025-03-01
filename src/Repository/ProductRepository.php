<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Retourne les produits paginés
     *
     * @param int $page
     * @param int $limit
     * @return array
     */
    public function findPaginatedProducts(int $page, int $limit, ?int $categoryId = null, ?int $tagId = null): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.isDeleted = false')
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.tags', 't')
            ->addSelect('c', 't');

        // Scénario 1 : Si uniquement les tags sont renseignés
        if ($tagId && !$categoryId) {
            $queryBuilder->andWhere('t.id = :tagId')
                ->setParameter('tagId', $tagId);
        }

        // Scénario 2 : Si uniquement les catégories sont renseignées
        if ($categoryId && !$tagId) {
            $queryBuilder->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        // Scénario 3 : Si les deux filtres sont renseignés
        if ($categoryId && $tagId) {
            $queryBuilder->andWhere('c.id = :categoryId AND t.id = :tagId')
                ->setParameter('categoryId', $categoryId)
                ->setParameter('tagId', $tagId);
        }

        // Scénario 4 : Aucun filtre → Retourne tous les produits (déjà géré par défaut)

        $queryBuilder->orderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        // Exécution de la requête avec Paginator
        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);

        return [
            'items' => $paginator,
            'totalItems' => count($paginator),
            'totalPages' => ceil(count($paginator) / $limit),
        ];
    }

    public function findPaginatedProductsByProvider(int $page, int $limit, int $providerId, ?int $categoryId = null, ?int $tagId = null): array
    {
        $offset = ($page - 1) * $limit;

        $queryBuilder = $this->createQueryBuilder('p')
            ->where('p.isDeleted = false')
            ->andWhere('p.provider = :providerId')
            ->setParameter('providerId', $providerId)
            ->leftJoin('p.categories', 'c')
            ->leftJoin('p.tags', 't')
            ->addSelect('c', 't');

        // Filtre par tag uniquement
        if ($tagId && !$categoryId) {
            $queryBuilder->andWhere('t.id = :tagId')
                ->setParameter('tagId', $tagId);
        }

        // Filtre par catégorie uniquement
        if ($categoryId && !$tagId) {
            $queryBuilder->andWhere('c.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        // Filtre par catégorie et tag
        if ($categoryId && $tagId) {
            $queryBuilder->andWhere('c.id = :categoryId AND t.id = :tagId')
                ->setParameter('categoryId', $categoryId)
                ->setParameter('tagId', $tagId);
        }

        $queryBuilder->orderBy('p.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $query = $queryBuilder->getQuery();
        $paginator = new Paginator($query);

        return [
            'items' => $paginator,
            'totalItems' => count($paginator),
            'totalPages' => ceil(count($paginator) / $limit),
        ];
    }
}
