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

use App\Entity\Authentication;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Authentication>
 */
class AuthenticationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Authentication::class);
    }

    public function findByToken(string $token): ?Authentication {
        return $this->createQueryBuilder('x')
            ->andWhere('x.token = :token AND x.status = 0')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByUserToken(string $token, User $user): ?Authentication {
        return $this->createQueryBuilder('x')
            ->andWhere('x.token = :token AND x.status = 0 AND x.user = :user')
            ->setParameter('token', $token)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
