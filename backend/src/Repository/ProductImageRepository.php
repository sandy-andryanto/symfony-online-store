<?php

/**
* This file is part of the Sandy Andryanto Online Store Website.
*
* @author     Sandy Andryanto <sandy.andryanto.blade@gmail.com>
* @copyright  2025
*
* For the full copyright and license information,
* please view the LICENSE.md file that was distributed
* with this source code.
*/

namespace App\Repository;

use App\Entity\ProductImage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductImage>
 */
class ProductImageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductImage::class);
    }

    public function getAll(int $id){

        $em = $this->getEntityManager();
        $result = $em->createQueryBuilder()
            ->select("x")
            ->from(ProductImage::class, 'x')
            ->join('x.product', 'p')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('x.sort', 'asc')
            ->getQuery()
            ->getArrayResult();

        return $result;
    }
}
