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

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\AbstractQuery;

/**
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }


    public function getDataTable(array $params, User $user)
    {

        $total_all  = (int) $this->createQueryBuilder('x')
            ->select('count(x.id)')
            ->andWhere('x.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getSingleScalarResult();

        $page   = array_key_exists("page", $params) ?  $params["page"] : 1;
        $limit   = array_key_exists("limit", $params) ?  $params["limit"] : 10;
        $search = array_key_exists("search", $params) ?  $params["search"] : null;
        $order_by   = array_key_exists("sort", $params) ?  $params["sort"] : 0;
        $order_sort = array_key_exists("dir", $params) ?  $params["dir"] : "desc";
        $offset  = (($page-1) * $limit);

       
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('x');
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

         if(!is_null($search)){
            $orStatements = $qb->expr()->orX();
            $orStatements->add($qb->expr()->like('x.invoiceNumber', $qb->expr()->literal('%' . $search . '%')));
            $qb->andWhere($orStatements);
        }
        
        $qb->addOrderBy('x.'.$order_by, $order_sort);
        $qb->andWhere('x.user = :user');
        $qb->setParameter('user', $user);
        $qb->from(Order::class, 'x');
        $query = $qb->getQuery()->getArrayResult();
        $total_filtered = count($query);

        return [
            "list" => $query,
            "total_all" => $total_all,
            "total_filtered" => $total_filtered,
            "limit"=> $limit,
            "page"=> $page  
        ];

    }

    public function findById($id)
    {
         return $this->createQueryBuilder('x')
            ->andWhere('x.id = :id')
            ->setParameter('id', $id)
            ->setMaxResults(1)
            ->orderBy('x.id', 'desc')
            ->getQuery()
            ->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY);
    }

    public function findByPending(User $user, bool $arr = false)
    {
         $data = $this->createQueryBuilder('x')
            ->join('x.user', 'u')
            ->andWhere('x.user = :user AND x.status = 0')
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->orderBy('x.id', 'desc')
            ->getQuery();

        return $arr ? $data->getOneOrNullResult(AbstractQuery::HYDRATE_ARRAY) : $data->getOneOrNullResult();
    }

    
}
