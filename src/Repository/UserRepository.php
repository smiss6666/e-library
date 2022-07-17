<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Contracts\UserInterface;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @method UserInterface|null find($id, $lockMode = null, $lockVersion = null)
 */
class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $username
     *
     * @return null|User|UserInterface
     */
    public function loadUserByUsername($username): ?\Symfony\Component\Security\Core\User\UserInterface
    {
        try {
            return $this->createQueryBuilder('u')
                ->where('u.username = :username OR u.email = :email')
                ->setParameters(['username' => $username, 'email' => $username])
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return null;
        }
    }
}
