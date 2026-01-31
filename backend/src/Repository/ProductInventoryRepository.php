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

use App\Entity\ProductInventory;
use App\Entity\Size;
use App\Entity\Colour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductInventory>
 */
class ProductInventoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductInventory::class);
    }

    public function getSizes(int $id){

        $em = $this->getEntityManager();
        $result = $em->createQueryBuilder()
            ->select("s.id, s.name")
            ->from(ProductInventory::class, 'x')
            ->join('x.product', 'p')
            ->join('x.size', 's')
            ->andWhere('p.id = :id AND x.stock > 0 AND x.status = 1 AND s.status = 1')
            ->groupBy("s.id, s.name")
            ->orderBy("s.name")
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    public function getColours(int $id){

        $em = $this->getEntityManager();
        $result = $em->createQueryBuilder()
            ->select("c.id, c.name, c.code")
            ->from(ProductInventory::class, 'x')
            ->join('x.product', 'p')
            ->join('x.colour', 'c')
            ->andWhere('p.id = :id AND x.stock > 0 AND x.status = 1 AND c.status = 1')
            ->groupBy("c.id, c.name, c.code")
            ->orderBy("c.name")
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    public function getAvailable(int $id){

        $em = $this->getEntityManager();
        $result = $em->createQueryBuilder()
            ->select("p.id, p.name, p.sku, p.image, p.price, COUNT(p.id) as stock")
            ->from(ProductInventory::class, 'x')
            ->join('x.product', 'p')
            ->andWhere('p.id = :id AND x.stock > 0 AND x.status = 1')
            ->groupBy("p.id, p.name, p.sku, p.image, p.price")
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();

        return $result;
    }

    public function getByColourAndSize(int $product_id, int $colour_id, int $size_id) : ?ProductInventory {
          return $this->createQueryBuilder('x')
            ->join('x.product', 'p')
            ->join('x.size', 's')
            ->join('x.colour', 'c')
            ->andWhere('x.size = :size AND x.colour = :colour AND x.product = :product')
            ->setParameter('colour', $colour_id)
            ->setParameter('size', $size_id)
            ->setParameter('product', $product_id)
            ->setMaxResults(1)
            ->orderBy('x.id', 'desc')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
