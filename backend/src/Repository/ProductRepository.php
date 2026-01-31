<?php

/**
* This file is part of the Sandy Andryanto Online Store Website.
*
* @author     Sandy Andryanto <sandy.andryanto.official@gmail.com>
* @copyright  2025
*
* For the full copyright and license information,
* please view the LICENSE.md file that was distributed
* with this source code.
*/

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;


/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getById(int $id){
        return $this->createQueryBuilder('x')
            ->andWhere('x.id = :id AND x.status = 1')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function getWhislist(int $user_id)
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('
               x.id,
               x.image,
               x.name,
               x.price
            ')
            ->from(Product::class, 'x')
            ->join('x.wishlists', 'u')
            ->groupBy('
               x.id,
               x.image,
               x.name,
               x.price
            ')
            ->getQuery()
            ->getArrayResult();
    }

    public function getGroupByBrand()
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('
                b.id,
                b.name,
                COUNT(x.id) as total_product
            ')
            ->from(Product::class, 'x')
            ->join('x.brand', 'b')
            ->groupBy('b.id, b.name')
            ->orderBy('b.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function getGroupByCategory()
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('
                c.id,
                c.name,
                COUNT(x.id) as total_product
            ')
            ->from(Product::class, 'x')
            ->join('x.categories', 'c')
            ->groupBy('c.id, c.name')
            ->orderBy('c.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function getProductByCategory(array $categories){
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        return $em->createQueryBuilder()
            ->select('x.id')
            ->from(Product::class, 'x')
            ->andWhere($qb->expr()->in('c.id', ':categories'))
            ->setParameter('categories', $categories)
            ->join('x.categories', 'c')
            ->groupBy('c.id, c.name')
            ->orderBy('c.name')
            ->getQuery()
            ->getArrayResult();
    }

    public function findAllPaged(array $params): array
    {
        $page           = array_key_exists("page", $params) ?  $params["page"] : 1;
        $limit          = array_key_exists("limit", $params) ?  $params["limit"] : 10;
        $search         = array_key_exists("search", $params) ?  $params["search"] : null;
        $order          = ["id", "DESC"];
        $offset         = (($page-1) * $limit);
        $bestSellers    = $this->getBestSellers();
        $maxRating      = count($bestSellers) > 0 ? $bestSellers[0]['totalRating']: 0;
        $dateNow        = date("Y-m-d");
        
        $total_all      = (int) $this->createQueryBuilder('x')
            ->select('count(x.id)')
            ->andWhere('x.status = 1 AND x.publishedDate <= :dateNow')
            ->setParameter('dateNow', $dateNow)
            ->getQuery()
            ->getSingleScalarResult();

        if(array_key_exists("sort", $params))
        {
            $sorts = explode(",", $params["sort"]);
            $order = [$sorts[0], $sorts[1]];
        }
        
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select("x");
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

         if(!is_null($search)){
            $orStatements = $qb->expr()->orX();
            $orStatements->add($qb->expr()->like('x.name', $qb->expr()->literal('%' . $search . '%')));
            $orStatements->add($qb->expr()->like('x.sku', $qb->expr()->literal('%' . $search . '%')));
            $orStatements->add($qb->expr()->like('x.description', $qb->expr()->literal('%' . $search . '%')));
            $qb->andWhere($orStatements);
        }

        if(array_key_exists("price", $params))
        {
            $range = explode("|", $params["price"]);
            $min = isset($range[0]) ? $range[0] : 0;
            $max = isset($range[1]) ? $range[1] : 0;
            $qb->andWhere("x.price >= :min AND x.price <= :max");
            $qb->setParameter('max', $max);
            $qb->setParameter('min', $min);
        }

        if(array_key_exists("brand", $params))
        {
            $brands = explode(",", $params["brand"]);
            $qb->andWhere($qb->expr()->in('b.id', ':brands'));
            $qb->setParameter('brands', $brands);
        }

        if(array_key_exists("category", $params))
        {
            $categories = explode(",", $params["category"]);
            $productIds = $this->getProductByCategory($categories);
            $productIds = array_map(fn($row) => $row["id"], $productIds);
            $qb->andWhere($qb->expr()->in('x.id', ':productIds'));
            $qb->setParameter('productIds', $productIds);
        }

        $qb->addOrderBy("x.".$order[0], $order[1]);
        $qb->andWhere('x.status = 1 AND x.publishedDate <= :dateNow');
        $qb->setParameter('dateNow', $dateNow);
        $qb->from(Product::class, 'x');
        $qb->join('x.brand', 'b');
        $productQuery = $qb->getQuery()->getResult();
        $total_filtered = count($productQuery);

        $products = array_map(function ($row) use ($maxRating) {

           $category = array_map(
                fn($category) => $category->getName(),
                $row->getCategories()->toArray()
            );

            $price = (float) $row->getPrice();
            $price_old = (float) $row->getPrice() + ($row->getPrice() * 0.05);
            $rating = $row->getTotalRating();

            if($maxRating != null){
                $rating = ((($row->getTotalRating() / $maxRating) * 100) / 20);
            }

            return [
                "id"=> $row->getId(),
                "name"=> $row->getName(),
                "image"=> $row->getImage(),
                "category"=> $category,
                "price"=> $price,
                "priceOld"=> $price_old,
                "totalRating"=> floor($rating)
            ];

        }, $productQuery);

        return [
            "list" => $products,
            "total_all" => $total_all,
            "total_filtered" => $total_filtered,
            "limit"=> $limit
        ];
    }

    public function getBestSellers()
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('x')
            ->from(Product::class, 'x')
            ->setMaxResults(3)
            ->orderBy('x.totalRating', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getDisplayedHome()
    {
        $bestSellers = $this->getBestSellers();
        $maxRating = count($bestSellers) > 0 ? $bestSellers[0]['totalRating']: 0;
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('
                x.id, 
                x.name, 
                x.price, 
                FLOOR((CASE WHEN '.$maxRating.' > 0 THEN (((x.totalRating/'.$maxRating.') * 100) / 20) ELSE x.totalRating END)) as totalRating,
                (x.price + (x.price * 0.05)) as priceOld, 
                x.image
            ')
            ->from(Product::class, 'x')
            ->setMaxResults(4)
            ->orderBy('x.id', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }
    
    public function getTopSelling(int $limit, string $order)
    {
        $bestSellers = $this->getBestSellers();
        $maxRating = count($bestSellers) > 0 ? $bestSellers[0]['totalRating']: 0;
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('
                x.id, 
                x.name, 
                x.price, 
                FLOOR((CASE WHEN '.$maxRating.' > 0 THEN (((x.totalRating/'.$maxRating.') * 100) / 20) ELSE x.totalRating END)) as totalRating,
                (x.price + (x.price * 0.05)) as priceOld, 
                x.image
            ')
            ->from(Product::class, 'x')
            ->setMaxResults($limit)
            ->orderBy($order, 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    public function getRecomended(int $limit, int $id)
    {
        $bestSellers = $this->getBestSellers();
        $maxRating = count($bestSellers) > 0 ? $bestSellers[0]['totalRating']: 0;
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('
                x.id, 
                x.name, 
                x.price, 
                FLOOR((CASE WHEN '.$maxRating.' > 0 THEN (((x.totalRating/'.$maxRating.') * 100) / 20) ELSE x.totalRating END)) as totalRating,
                (x.price + (x.price * 0.05)) as priceOld, 
                x.image
            ')
            ->from(Product::class, 'x')
            ->andWhere('x.id != :id')
            ->setParameter('id', $id)
            ->setMaxResults($limit)
            ->orderBy("x.totalOrder", 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

}
