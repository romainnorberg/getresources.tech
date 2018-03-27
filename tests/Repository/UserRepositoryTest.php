<?php

namespace App\Tests\Repository;

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
     * @dataProvider loadUserByUsernameProvider
     * @covers       \App\Repository\UserRepository::loadUserByUsername
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

    public function loadUserByUsernameProvider()
    {
        yield ['jmalkovich']; // username
        yield ['john@malkovich.com']; // email
    }

    /**
     * @dataProvider createIfNotExistExistingUserProvider
     *
     * @param string $username
     *
     * @throws NonUniqueResultException
     */
    public function testCreateIfNotExistShouldReturnExistingUser(string $username): void
    {
        $user = $this->repository->createIfNotExist($username);

        $this->assertNotEmpty($user->getId());
    }

    public function createIfNotExistExistingUserProvider()
    {
        // existing username
        yield ['jmalkovich'];

        // existing email
        yield ['john@malkovich.com'];
    }

    /**
     * @dataProvider createIfNotExistUnknownUserProvider
     *
     * @param string $username
     *
     * @throws NonUniqueResultException
     */
    public function testCreateIfNotExistShouldReturnNewUser(string $username): void
    {
        $user = $this->repository->createIfNotExist($username);

        $this->assertNull($user->getId());
    }

    public function createIfNotExistUnknownUserProvider()
    {
        // Unknown username
        yield ['sjobs'];

        // Unknown email
        yield ['sj@apple.com'];
    }

    /**
     * @expectedException \Doctrine\DBAL\Exception\NotNullConstraintViolationException
     * @throws NonUniqueResultException
     */
    public function testSaveWithoutPasswordShouldReturnException(): void
    {
        $newUser = $this->repository->createIfNotExist('benharper');
        $this->repository->save($newUser);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function testSaveShouldEntityManagerContainsFakeUsername(): void
    {
        $faker = \Faker\Factory::create();

        $newUser = $this->repository->createIfNotExist($faker->userName);
        $newUser->setEmail($faker->email);
        $newUser->setPlainPassword($faker->password);

        $newUser = $this->repository->save($newUser);

        $this->assertTrue($this->em->contains($newUser));
    }
}