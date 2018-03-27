<?php

namespace App\Repository;

use App\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    /**
     * @param string $username
     *
     * @return mixed|null|\Symfony\Component\Security\Core\User\UserInterface
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadUserByUsername($username): ? User
    {
        $query = $this->createQueryBuilder('u')
            ->where('u.username = :username OR u.email = :email')
            ->setParameter('username', $username)
            ->setParameter('email', $username)
            ->getQuery();

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $username
     *
     * @return User
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function createIfNotExist(string $username): User
    {
        if (null !== $user = $this->loadUserByUsername($username)) {
            return $user;
        }

        $newUser = User::create();
        $newUser->setUsername($username);

        return $newUser;
    }

    public function save(User $user): User
    {
        $this->_em->persist($user);
        $this->_em->flush($user);

        return $user;
    }
}