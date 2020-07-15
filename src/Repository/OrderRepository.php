<?php

namespace App\Repository;

use App\Entity\Order;
use DateInterval;
use DateTime;
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

    public function getByDateByAuthor(\Datetime $date,$user)
        {
            $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
            $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

            $qb = $this->createQueryBuilder("e");
            $qb
                ->andWhere('e.created BETWEEN :from AND :to')
                ->setParameter('to', $to)
                ->setParameter('from', $from )
                ->andWhere('e.user = :user')
                ->setParameter('user', $user )
                ->andWhere('e.status = :status')
                ->setParameter('status', 'finish' )
            ;
            $result = $qb->getQuery()->getResult();

            return $result;
        }

    public function getCashFlowWithDate(\Datetime $date,$user){
            $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
            $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");
            
            $TotalCommission = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'finish')
            ->leftJoin('a.user', 'u')
            ->andWhere('u.roles = :roles')
            ->setParameter('roles', '["ROLE_AUTHOR"]')
            ->andWhere('a.created BETWEEN :from AND :to')
            ->setParameter('to', $to)
            ->setParameter('from', $from )
            ->select('SUM(a.amount)')
            ->getQuery()
            ->getSingleScalarResult();
            ;
            $TotalCommission = $TotalCommission * 0.20;

            
            $EurCommandAdmin = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'finish')
            ->leftJoin('a.user', 'u')
            ->andwhere('u.roles = :roles')
            ->setParameter('roles', '["ROLE_ADMIN"]')
            ->andWhere('a.created BETWEEN :from AND :to')
            ->setParameter('to', $to)
            ->setParameter('from', $from )
            ->select('SUM(a.amount)')
            ->getQuery()
            ->getSingleScalarResult();
            ;
            

            return $EurCommandAdmin + $TotalCommission;
    }

    public function getTotalOrderAmountForOneAuthorWithRemoveCommision($user){

        $TotalAmountCommandAuthor = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'finish')
            ->leftJoin('a.user', 'u')
            ->andwhere('u.id = :user')
            ->setParameter('user', $user)
            ->select('SUM(a.amount)')
            ->getQuery()
            ->getSingleScalarResult();
            ;

        return $TotalAmountCommandAuthor * 0.80;
    }

    public function getTotalAmountGeneratedIn30LastDaysForAuthorWithRemoveCommision($user){
        $date = new DateTime();
        $last30Days = new DateTime('-30days');

        // CA Author last 30 days * 0.80 
        $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
        $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

        $TotalAmountCommandAuthor = $this
            ->createQueryBuilder('a')
            ->where('a.status = :status')
            ->setParameter('status', 'finish')
            ->leftJoin('a.user', 'u')
            ->andwhere('u.id = :user')
            ->setParameter('user', $user)
            ->andWhere('a.created BETWEEN :from AND :to')
            ->setParameter('to', $date)
            ->setParameter('from', $last30Days )
            ->select('SUM(a.amount)')
            ->getQuery()
            ->getSingleScalarResult();
            ;

        return $TotalAmountCommandAuthor * 0.80;

        
    }

}
