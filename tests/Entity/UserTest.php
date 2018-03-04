<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\AppWebTestCase;

class UserTest extends AppWebTestCase
{
    /**
     * @covers User
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testPersistSetsTimestamps(): void
    {
        $dateTime = new \DateTime('now');

        $user = new User();
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
     * @covers User::setUsername
     */
    public function testToString(): void
    {
        $user = new User();

        $user->setUsername('example');
        $this->assertEquals('example', (string)$user);
    }

    /**
     * @covers User::setUsername
     */
    public function testUsername(): void
    {
        $user = new User();

        $user->setUsername('example');
        $this->assertEquals('example', $user->getUsername());
    }

    /**
     * @covers User::setEmail
     */
    public function testEmail(): void
    {
        $user = new User();

        $user->setEmail('mail@example.org');
        $this->assertEquals('mail@example.org', $user->getEmail());
    }

    /**
     * @covers User::setPlainPassword
     */
    public function testPassword(): void
    {
        $user = new User();
        $this->assertNull($user->getPassword());

        $password = 'example';
        $user->setPlainPassword($password);
        $this->assertNotEquals($password, $user->getPassword());
    }

    /**
     * @covers User
     */
    public function testActivation(): void
    {
        $user = new User();
        $this->assertTrue($user->getisActive());
    }
}