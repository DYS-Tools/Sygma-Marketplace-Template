<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    // ADMIN
    public function findAllTProductNoVerified()
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.verified = 0');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function countAllProductForSell()
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.verified = 1');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countAllProductForVerified()
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.verified = 0');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    // USER
    public function findAllTProductVerified()
    {
        $qb = $this->createQueryBuilder('p')
            ->where('p.verified = 1');
        $query = $qb->getQuery();
        return $query->execute();
    }

    public function findLike($name)
    {
        return $this
            ->createQueryBuilder('a')
            ->where('a.name LIKE :name')
            ->setParameter( 'name', "%$name%")
            ->orderBy('a.name')
            ->setMaxResults(20)
            ->getQuery()
            ->execute()
            ;
    }
}
