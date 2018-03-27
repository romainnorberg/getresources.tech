<?php

namespace App\Tests\Utils;

use App\Entity\User;
use App\Vo\Auth\GithubUserResponseVo;
use PHPUnit\Framework\TestCase;

/**
 * Class UserUtilsTest
 * @package App\Tests\Utils
 */
class UserUtilsTest extends TestCase
{
    public function testShouldPopulateFromGithubUserResponseVoReturnPopulatedEntity(): void
    {
        $faker = \Faker\Factory::create();

        $login = $faker->userName;
        $email = $faker->email;

        $githubUserResponseVo = new GithubUserResponseVo();
        $githubUserResponseVo->login = $login;
        $githubUserResponseVo->email = $email;

        $user = User::create();
        $user->getUtils()->populateFromGithubUserResponseVo($githubUserResponseVo);

        $this->assertEquals($login, $user->getUsername());
        $this->assertEquals($email, $user->getEmail());
    }
}