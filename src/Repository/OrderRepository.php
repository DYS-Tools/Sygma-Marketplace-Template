<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    // ADMIN
    public function countAllOrder()
    {
        $qb = $this->createQueryBuilder('e');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countOrderAdminEur(){
        // Sum of cash for admin product

        return $this
            
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'finish')
            ->leftJoin('a.user', 'u')
            ->andwhere('u.roles = :roles')
            ->setParameter('roles', '["ROLE_ADMIN"]')
            ->select('SUM(a.amount)')
            ->getQuery()
            ->getSingleScalarResult();
            ;
    }

    public function countTotalCommissions(){
        // Total order without admin * 0.20 

        $TotalOrderAuthor = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'finish')
            ->leftJoin('a.user', 'u')
            ->andWhere('u.roles = :roles')
            ->setParameter('roles', '["ROLE_AUTHOR"]')
            ->select('SUM(a.amount)')
            ->getQuery()
            ->getSingleScalarResult();
            ;

        return $TotalOrderAuthor * 0.20;
    }

    //USER

    // for validation order in payment
    public function findLastOrderOfUser($user) {
        return $this->createQueryBuilder('a')
            ->setParameter('user', $user)
            ->where('a.user = :user')
            ->orderBy('a.created', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()
            ;
    }
}
