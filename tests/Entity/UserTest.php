<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\AppWebTestCase;

class UserTest extends AppWebTestCase
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testPersistSetsTimestamps()
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

    public function testToString()
    {
        $user = new User();

        $user->setUsername('example');
        $this->assertEquals('example', (string)$user);
    }

    public function testUsername()
    {
        $user = new User();

        $user->setUsername('example');
        $this->assertEquals('example', $user->getUsername());
    }

    public function testEmail()
    {
        $user = new User();

        $user->setEmail('mail@example.org');
        $this->assertEquals('mail@example.org', $user->getEmail());
    }

    public function testPassword()
    {
        $user = new User();
        $this->assertNull($user->getPassword());

        $password = 'example';
        $user->setPlainPassword($password);
        $this->assertNotEquals($password, $user->getPassword());
    }

    public function testActivation()
    {
        $user = new User();
        $this->assertTrue($user->getisActive());
    }
}