<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\AppWebTestCase;

class UserTest extends AppWebTestCase
{


    /**
     * @covers \App\Entity\User
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testPersistSetsTimestamps(): void
    {
        $dateTime = new \DateTime('now');

        $user = User::create();
        $user->setUsername('example@example.com');
        $user->setEmail('example@example.com');
        $user->setPlainPassword('password');

        $this->em->persist($user);
        $this->em->flush();

        $this->assertGreaterThan($dateTime, $user->getCreated());
        $this->assertGreaterThan($dateTime, $user->getUpdated());

        $this->em->remove($user);
        $this->em->flush();
    }

    /**
     * @covers \App\Entity\User::setUsername
     */
    public function testToString(): void
    {
        $user = User::create();

        $user->setUsername('example');
        $this->assertEquals('example', (string)$user);
    }

    /**
     * @covers \App\Entity\User::setUsername
     */
    public function testUsername(): void
    {
        $user = User::create();

        $user->setUsername('example');
        $this->assertEquals('example', $user->getUsername());
    }

    /**
     * @covers \App\Entity\User::setEmail
     */
    public function testEmail(): void
    {
        $user = User::create();

        $user->setEmail('mail@example.org');
        $this->assertEquals('mail@example.org', $user->getEmail());
    }

    /**
     * @covers \App\Entity\User::setPlainPassword
     */
    public function testPassword(): void
    {
        $user = User::create();
        $this->assertNull($user->getPassword());

        $password = 'example';
        $user->setPlainPassword($password);
        $this->assertNotEquals($password, $user->getPassword());
    }

    /**
     * @covers \App\Entity\User
     */
    public function testActivation(): void
    {
        $user = User::create();
        $this->assertTrue($user->getisActive());
    }

    /**
     * @covers \App\Entity\User
     */
    public function testShouldUserHasRoleOnCreate(): void
    {
        $user = User::create();

        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    /**
     * @covers \App\Entity\User::create()
     *
     * @throws \ReflectionException
     */
    public function testShouldCreateRequireNoParameter(): void
    {
        $reflection = new \ReflectionMethod(User::class, 'create');

        $this->assertEmpty($reflection->getParameters(), sprintf('Create method require no parameter. Actual parameters: %s', implode(', ', $reflection->getParameters())));
    }

    /**
     * @covers \App\Entity\User::create()
     */
    public function testShouldCreateReturnNewUserEntity(): void
    {
        $user = User::create();

        $this->assertInstanceOf(User::class, $user, sprintf('New instance of App\Entity\User expected, return %s', \get_class($user)));
    }
}