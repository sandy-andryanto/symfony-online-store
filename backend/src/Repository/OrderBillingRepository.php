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

use App\Entity\OrderBilling;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderBilling>
 */
class OrderBillingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderBilling::class);
    }

    public function deleteByOrder($order)
    {
        $qb = $this->createQueryBuilder('x');
        $qb->delete(OrderBilling::class, 'x')
            ->where('x.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->execute();
    }

    public function getBilling($order)
    {
        $data = [];
        
        $result = $this->createQueryBuilder('x')
            ->where('x.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getResult();

        foreach($result as $row){
            $data[$row->getName()] = $row->getDescription();
        }
        
        return $data;
    }

}
