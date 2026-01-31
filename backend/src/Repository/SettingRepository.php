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

use App\Entity\Setting;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Setting>
 */
class SettingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Setting::class);
    }

    public function getByKey(string $keyName) {
        
        $result = $this->createQueryBuilder('x')
            ->andWhere('x.keyName = :keyName')
            ->setParameter('keyName', $keyName)
            ->getQuery()
            ->getOneOrNullResult();

        return is_null($result) ? $keyName : $result->getKeyValue();
    }

    public function getAll()
    {
        $data = [];
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(["x.keyName", "x.keyValue"]);
        $qb->where("x.status = 1");
        $qb->from(Setting::class, 'x');
        $result = $qb->getQuery()->getResult();

        foreach($result as $row){
            $data[$row['keyName']] = $row['keyValue'];
        }

        return $data;
    }
}
