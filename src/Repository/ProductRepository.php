<?php

namespace App\Repository;

use App\CustomClass\Search;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     * Get products by user search
     * @return Product[] Returns an array of Product
     */
    public function findWithSearch(Search $search)
    {
        $query = $this
            -> createQueryBuilder('p') //product
            -> select('c', 'p') // category && product
            -> join('p.category', 'c');

        if (!empty($search -> categories) || !empty($search -> string)) {
            $query = $query
                -> andWhere('c.id IN (:categories)')
                -> andWhere('p.name LIKE :string OR p.brand LIKE :string')
                -> setParameter('categories', $search -> categories)
                -> setParameter('string', "%{$search -> string}%");
        }

//        if (!empty($search -> string)) {
//            $query = $query
//                -> andWhere('p.name LIKE :string OR p.brand LIKE :string')
//                -> setParameter('string', "%{$search -> string}%");
//        }

        return $query -> getQuery() -> getResult();
    }
}
