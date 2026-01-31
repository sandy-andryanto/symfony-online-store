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

use App\Entity\OrderDetail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderDetail>
 */
class OrderDetailRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OrderDetail::class);
    }

    public function deleteByOrder($order)
    {
        $qb = $this->createQueryBuilder('x');
        $qb->delete(OrderDetail::class, 'x')
            ->where('x.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->execute();
    }

    public function findAllByOrder($order)
    {
        return $this->createQueryBuilder('x')
            ->andWhere('x.order = :order')
            ->setParameter('order', $order)
            ->getQuery()
            ->getResult();
    }

    public function getListOrder($order_id)
    {
        return $this->createQueryBuilder('x')
            ->select('
                p.id,
                p.name,
                p.image,
                p.price,
                SUM(x.qty) as total_item,
                (x.price * SUM(x.qty)) as subtotal
            ')
            ->andWhere('o.id = :order_id')
            ->setParameter('order_id', $order_id)
            ->join('x.order', 'o')
            ->join('x.inventory', 'inv')
            ->join('inv.product', 'p')
            ->groupBy('
                p.id,
                p.name,
                p.image,
                p.price
            ')
            ->getQuery()
            ->getResult();
    }

    public function getByInventory($order_id, $inventory_id)
    {
        return $this->createQueryBuilder('x')
            ->andWhere('o.id = :order_id AND inv.id = :inventory_id')
            ->setParameter('order_id', $order_id)
            ->setParameter('inventory_id', $inventory_id)
            ->join('x.order', 'o')
            ->join('x.inventory', 'inv')
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getSubtotal($order_id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('SUM(x.total)')
            ->andWhere('o.id = :order_id')
            ->setParameter('order_id', $order_id)
            ->from(OrderDetail::class, 'x')
            ->join('x.order', 'o');
        $sum = $qb->getQuery()->getSingleScalarResult();
        return $sum;
    }

     public function getTotalQty($order_id)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('SUM(x.qty)')
            ->andWhere('o.id = :order_id')
            ->setParameter('order_id', $order_id)
            ->from(OrderDetail::class, 'x')
            ->join('x.order', 'o');
        $sum = $qb->getQuery()->getSingleScalarResult();
        return $sum;
    }
}
