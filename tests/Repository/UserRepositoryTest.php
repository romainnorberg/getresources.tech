<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    /**
     * @var \App\Repository\UserRepository
     */
    protected $repository;

    /**
     * @var EntityManager
     */
    protected $em;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->em = $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager');
        $this->repository = $this->em->getRepository('App:User');
    }

    /**
     * @dataProvider usernameProvider
     * @covers       UserRepository::loadUserByUsername
     *
     * @param string $username
     *
     * @throws NonUniqueResultException
     */
    public function testLoadUserByUsername(string $username): void
    {
        try {
            $user = $this->repository->loadUserByUsername($username);
        } catch (NonUniqueResultException $e) {
            throw $e;
        }
        $this->assertEquals('jmalkovich', $user->getUsername());
    }

    public function usernameProvider()
    {
        yield ['jmalkovich']; // username
        yield ['john@malkovich.com']; // email
    }
}