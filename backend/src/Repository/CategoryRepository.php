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

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    

    public function getDisplayed()
    {
        $em = $this->getEntityManager();
        return $em->createQueryBuilder()
            ->select('x')
            ->from(Category::class, 'x')
            ->where('x.displayed = :displayed')
            ->setParameter('displayed', 1)
            ->setMaxResults(3)
            ->getQuery()
            ->getArrayResult();
    }
}
