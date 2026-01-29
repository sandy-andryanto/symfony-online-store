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

use App\Entity\ProductReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Product;

/**
 * @extends ServiceEntityRepository<ProductReview>
 */
class ProductReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductReview::class);
    }

    public function getAll(int $id){

        $em = $this->getEntityManager();
        $result = $em->createQueryBuilder()
            ->select('
                x.id,
                x.createdAt,
                u.firstName,
                u.lastName,
                x.rating,
                x.review
            ')
            ->from(ProductReview::class, 'x')
            ->join('x.product', 'p')
            ->join('x.user', 'u')
            ->andWhere('p.id = :id')
            ->setParameter('id', $id)
            ->orderBy('x.id', 'desc')
            ->getQuery()
            ->getResult();

        return array_map(function ($row)  {
            $rating_index = ($row["rating"]/100) * 5;
            return [
                "id"=> $row["id"],
                "createdAt"=> $row["createdAt"]->format('Y-m-d H:i:s'),
                "user"=> [
                    "firstName"=> $row["firstName"],
                    "lastName"=> $row["lastName"]
                ],
                "rating"=> $row["rating"],
                "review"=> $row["review"],
                "ratingIndex"=> ceil($rating_index)
            ];
        }, $result);
    }
    
}
