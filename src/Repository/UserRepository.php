<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    // ADMIN
    public function countAllMember()
    {
        $qb = $this->createQueryBuilder('e');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countAllUser()
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_USER"%');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countAllAuthor()
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_AUTHOR"%');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countAllAdmin()
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_ADMIN"%');
        $qb ->select($qb->expr()->count('e'));
        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countCurrentAvailablePayout(){
        // Available_payout SUM // Without ADMIN

        return $this
            ->createQueryBuilder('a')
            ->where('a.roles LIKE :roles')
            ->setParameter('roles', '%"ROLE_AUTHOR"%')
            ->select('SUM(a.available_payout) as payout')
            ->getQuery()
            ->getSingleScalarResult();

            ;
    }
}
