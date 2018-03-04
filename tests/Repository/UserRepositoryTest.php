<?php

namespace App\Tests\Repository;

use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserRepositoryTest extends WebTestCase
{
    /**
     * @var \App\Repository\UserRepository
     */
    protected $repository;

    protected function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $this->repository = $kernel
            ->getContainer()
            ->get('doctrine.orm.entity_manager')
            ->getRepository('App:User');
    }

    /**
     * @dataProvider usernameProvider
     * @covers       UserRepository::loadUserByUsername()
     *
     * @param $username
     *
     * @throws NonUniqueResultException
     */
    public function testLoadUserByUsername($username): void
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