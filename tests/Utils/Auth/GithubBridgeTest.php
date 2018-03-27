<?php

namespace App\Tests\Utils\Auth;

use App\Utils\Auth\Github;
use App\Utils\Auth\GithubBridge;
use App\Vo\Auth\GithubAuthenticateResponseVo;
use App\Vo\Auth\GithubUserResponseVo;
use Faker\Provider\Miscellaneous;
use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use League\Uri;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class GithubBridgeTest
 * @package App\Tests\Utils\Auth
 */
class GithubBridgeTest extends TestCase
{
    /**
     * @covers \App\Utils\Auth\GithubBridge
     */
    public function testCouldBeConstructedWithSessionAsFirstArgument()
    {
        new GithubBridge(new Session(), new Github(new Client()));

        $this->assertFalse($this->hasFailed());
    }

    /**
     * @covers \App\Utils\Auth\GithubBridge::authorize
     */
    public function testShouldReturnRedirectResponseOnAuthorizeWithStateValue()
    {
        $session = new Session();
        $githubBridge = new GithubBridge($session, new Github(new Client()));

        /* @var $authorizeResponse RedirectResponse */
        $authorizeResponse = $githubBridge->authorize();

        // check response type
        $this->assertInstanceOf(RedirectResponse::class, $authorizeResponse);

        $authorizeUrlObject = Uri\Http::createFromString($authorizeResponse->getTargetUrl());

        // check host + path
        $this->assertEquals('https', $authorizeUrlObject->getScheme());
        $this->assertEquals('github.com', $authorizeUrlObject->getHost());
        $this->assertEquals('/login/oauth/authorize', $authorizeUrlObject->getPath());

        // check queries
        $authorizeUrlQueries = Uri\parse_query($authorizeUrlObject->getQuery());
        $this->assertEquals('99999999999999', $authorizeUrlQueries['client_id']);
        $this->assertEquals('http://getresources.local/auth/github/callback', $authorizeUrlQueries['redirect_uri']);
        $this->assertEquals('read:user', $authorizeUrlQueries['scope']);
        $this->assertEquals($session->get('githubAuthState'), $authorizeUrlQueries['state']);
    }

    /**
     * @covers \App\Utils\Auth\GithubBridge::authenticate()
     */
    public function testShouldReturnGithubAuthenticateResponseOnAuthenticateWithValidArguments()
    {
        $state = Miscellaneous::sha1();
        $code = Miscellaneous::md5();

        $clientMock = $this->createClientMock();
        $clientMock
            ->method('request')
            ->willReturn(new MessageInterfaceAccessTokenStub());

        $githubBridge = new GithubBridge(new Session(), new Github($clientMock));
        /* @var $authenticateResponse GithubAuthenticateResponseVo */
        $authenticateResponse = $githubBridge->authenticate($code, $state);

        // check response type
        $this->assertInstanceOf(GithubAuthenticateResponseVo::class, $authenticateResponse);
        $this->assertEquals('c0cb03704d5a26c35546af163058f139a9dca6a1', $authenticateResponse->access_token);
        $this->assertEquals('bearer', $authenticateResponse->token_type);
        $this->assertEquals('read:user', $authenticateResponse->scope);

        return $authenticateResponse;
    }

    /**
     * @depends testShouldReturnGithubAuthenticateResponseOnAuthenticateWithValidArguments
     * @covers  \App\Utils\Auth\GithubBridge::getUserDetails()
     */
    public function testShouldReturnGithubUserResponseOnGetUserDetailsWithValidArguments(GithubAuthenticateResponseVo $authenticateResponse)
    {
        $clientMock = $this->createClientMock();
        $clientMock
            ->method('request')
            ->willReturn(new MessageInterfaceUserStub());

        $githubBridge = new GithubBridge(new Session(), new Github($clientMock));
        /* @var $githubUserResponseVo GithubUserResponseVo */
        $githubUserResponseVo = $githubBridge->getUserDetails($authenticateResponse->access_token);

        // check response type
        $this->assertInstanceOf(GithubUserResponseVo::class, $githubUserResponseVo);
        $this->assertEquals('romainnorberg', $githubUserResponseVo->login);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Client
     */
    private function createClientMock()
    {
        return $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['request'])
            ->getMock();
    }
}

class MessageInterfaceAccessTokenStub
{
    public function getBody()
    {
        return new StreamInterfaceAccessTokenStub();
    }
}

class StreamInterfaceAccessTokenStub
{
    public function getContents()
    {
        return file_get_contents(__DIR__ . '/../../../Resources/fixtures/Auth/Github/access_token.json');
    }
}


class MessageInterfaceUserStub
{
    public function getBody()
    {
        return new StreamInterfaceUserStub();
    }
}

class StreamInterfaceUserStub
{
    public function getContents()
    {
        return file_get_contents(__DIR__ . '/../../../Resources/fixtures/Auth/Github/user_01.json');
    }
}