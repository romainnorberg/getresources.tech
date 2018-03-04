<?php

namespace App\Tests\Utils\Auth;

use App\Utils\Auth\Github;
use Faker\Provider\Miscellaneous;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use League\Uri;

/**
 * Class GithubTest
 * @package App\Tests\Utils\Auth
 */
class GithubTest extends TestCase
{
    /**
     * @var Github
     */
    private $githubService;

    public function setUp()
    {
        $this->githubService = new Github(new Client());

        parent::setUp();
    }

    /**
     * @covers \App\Utils\Auth\Github::authorize
     * @expectedException \ArgumentCountError
     */
    public function testShouldReturnErrorOnAuthorizeWithoutExtraParams()
    {
        $this->githubService->authorize();
    }

    /**
     * @covers \App\Utils\Auth\Github::authorize
     * @expectedException \InvalidArgumentException
     */
    public function testShouldReturnErrorOnAuthorizeWithoutStateValue()
    {
        $this->githubService->authorize([]);
    }

    /**
     * @covers \App\Utils\Auth\Github::authorize
     */
    public function testShouldReturnValidUrlOnAuthorizeWithStateValue()
    {
        $state = Miscellaneous::sha1();

        $authorizeUrl = $this->githubService->authorize(['state' => $state]);

        // ex: https://github.com/login/oauth/authorize?client_id=99999999999999&redirect_uri=http%3A%2F%2Fgetresources.local%2Fauth%2Fgithub%2Fcallback&scope=read%3Auser&state=a3769fcedb127abe2bd8e60c7f4ae1f1
        $authorizeUrlObject = Uri\Http::createFromString($authorizeUrl);

        // check host + path
        $this->assertEquals('https', $authorizeUrlObject->getScheme());
        $this->assertEquals('github.com', $authorizeUrlObject->getHost());
        $this->assertEquals('/login/oauth/authorize', $authorizeUrlObject->getPath());

        // check queries
        $authorizeUrlQueries = Uri\parse_query($authorizeUrlObject->getQuery());
        $this->assertEquals('99999999999999', $authorizeUrlQueries['client_id']);
        $this->assertEquals('http://getresources.local/auth/github/callback', $authorizeUrlQueries['redirect_uri']);
        $this->assertEquals('read:user', $authorizeUrlQueries['scope']);
        $this->assertEquals($state, $authorizeUrlQueries['state']);
    }

    /**
     * @covers \App\Utils\Auth\Github::authenticate()
     * @expectedException \ArgumentCountError
     */
    public function testShouldReturnErrorOnAuthenticateWithMissingCodeAndStateArguments()
    {
        $this->githubService->authenticate();
    }

    /**
     * @covers \App\Utils\Auth\Github::authenticate()
     * @expectedException \ArgumentCountError
     */
    public function testShouldReturnErrorOnAuthenticateWithMissingStateArgument()
    {
        $code = Miscellaneous::md5();

        $this->githubService->authenticate($code);
    }

    /**
     * @covers \App\Utils\Auth\Github::authenticate()
     */
    public function testShouldReturnArrayOnAuthenticateWithValidArguments()
    {
        $state = Miscellaneous::sha1();
        $code = Miscellaneous::md5();

        // mock \GuzzleHttp\Client
        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('request')
            ->with('POST', 'https://github.com/login/oauth/access_token', [
                'form_params' => [
                    'client_id'     => '99999999999999',
                    'client_secret' => '99999999999999',
                    'code'          => $code,
                    'redirect_uri'  => 'http://getresources.local/auth/github/callback',
                    'state'         => $state,
                ],
                'headers'     => [
                    'Accept'     => 'application/json',
                    'User-Agent' => 'getresources.tech',
                ],
            ])
            ->willReturn(new MessageInterfaceStub());

        $githubService = new Github($clientMock);
        $response = $githubService->authenticate($code, $state);

        $this->assertInternalType('array', $response);
    }

    /**
     * @covers \App\Utils\Auth\Github::getUserDetails()
     */
    public function testShouldReturnArrayOnGetUserDetailsWithValidArguments()
    {
        $accessToken = Miscellaneous::sha1();

        // mock \GuzzleHttp\Client
        $clientMock = $this->createClientMock();
        $clientMock
            ->expects($this->once())
            ->method('request')
            ->with('GET', 'https://api.github.com/user', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'User-Agent'    => 'getresources.tech',
                    'Accept'        => 'application/json',
                    'Cache-Control' => 'no-cache',
                ],
            ])
            ->willReturn(new MessageInterfaceStub());

        $githubService = new Github($clientMock);
        $response = $githubService->getUserDetails($accessToken);

        $this->assertInternalType('array', $response);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private function createClientMock()
    {
        return $this->createMock(Client::class);
    }
}

class MessageInterfaceStub
{
    public function getBody()
    {
        return new StreamInterfaceStub();
    }
}

class StreamInterfaceStub
{
    public function getContents()
    {
        return '{}'; // encoded json
    }
}