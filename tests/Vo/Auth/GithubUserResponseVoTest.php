<?php

namespace App\Tests\Vo\Auth;

use App\Vo\Auth\GithubUserResponseVo;
use PHPUnit\Framework\TestCase;

/**
 * Class GithubUserResponseVoTest
 * @package App\Tests\Vo
 *
 * @covers  \App\Vo\Auth\GithubUserResponseVo
 */
class GithubUserResponseVoTest extends TestCase
{
    /**
     * @dataProvider dataProperties()
     *
     * @param string $property
     */
    public function testProperties(string $property): void
    {
        $this->assertClassHasAttribute($property, GithubUserResponseVo::class, "Class GithubUserResponseVo has '$property' property.");
    }

    public function dataProperties(): array
    {
        return [
            ['login'],
            ['id'],
            ['avatar_url'],
            ['gravatar_id'],
            ['url'],
            ['html_url'],
            ['followers_url'],
            ['following_url'],
            ['gists_url'],
            ['starred_url'],
            ['subscriptions_url'],
            ['organizations_url'],
            ['repos_url'],
            ['events_url'],
            ['received_events_url'],
            ['type'],
            ['site_admin'],
            ['name'],
            ['company'],
            ['blog'],
            ['location'],
            ['email'],
            ['hireable'],
            ['bio'],
            ['public_repos'],
            ['public_gists'],
            ['followers'],
            ['following'],
            ['created_at'],
            ['updated_at'],
            ['private_gists'],
            ['total_private_repos'],
            ['owned_private_repos'],
            ['disk_usage'],
            ['collaborators'],
            ['two_factor_authentication'],
            ['plan'],
        ];
    }
}