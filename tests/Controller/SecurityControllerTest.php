<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\BrowserKit\Cookie;

class SecurityControllerTest extends AppWebTestCase
{
    private $filterXPathForm = "//*[@name='login_form']";

    public function testSubmitLoginFormWithMissingPassword()
    {
        $url = $this->client->getContainer()->get('router')->generate('login', []);
        $crawler = $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        // Form
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('Login', $response->getContent());

        $values = [
            '_username' => 'jmalkovich',
        ];

        //
        $form = $crawler->filterXPath($this->filterXPathForm)->form();
        $form->setValues($values);
        $this->client->submit($form);

        // Redirect
        $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('Invalid credentials.', $response->getContent());
    }

    /**
     * @depends testSubmitLoginFormWithMissingPassword
     */
    public function testSubmitRegisterFormWithInvalidPassword()
    {
        $url = $this->client->getContainer()->get('router')->generate('login', []);
        $crawler = $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        // Form
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('Login', $response->getContent());

        $values = [
            '_username' => 'jmalkovich',
            '_password' => 'invalid_password',
        ];

        //
        $form = $crawler->filterXPath($this->filterXPathForm)->form();
        $form->setValues($values);
        //$form['_remember_me']->tick(); // tick remind-me checkbox TODO
        $this->client->submit($form);

        // Redirect
        $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('Invalid credentials.', $response->getContent());
    }

    /**
     * You can login with username OR email address
     *
     * @covers       \App\Controller\SecurityController::login()
     * @dataProvider usernameProvider
     * @depends      testSubmitRegisterFormWithInvalidPassword
     *
     * @param $username
     */
    public function testLoginActionLoginForm($loginUsername, $username)
    {
        // Get Url
        $url = $this->client->getContainer()->get('router')->generate('login', []);
        $crawler = $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // 1: show form
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains('Login', $response->getContent());

        $values = [
            '_username' => $loginUsername,
            '_password' => '12345',
        ];

        // 2: fill form with error
        $form = $crawler->filterXPath($this->filterXPathForm)->form();
        $form->setValues($values);
        //$form['_remember_me']->tick(); // tick remind-me checkbox TODO
        $this->client->submit($form);

        // add session cookie
        $session = $this->client->getContainer()->get('session');
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

        // Redirect to login page (referer), but user is now log-in
        $this->client->followRedirect();

        // Redirect to homepage (default)
        $this->client->followRedirect();
        $response = $this->client->getResponse();

        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertContains($username, $response->getContent()); // header

        // logout user
        $url = $this->client->getContainer()->get('router')->generate('logout', []);
        $this->client->request('GET', $url);
        $this->client->followRedirect();
    }

    public function usernameProvider()
    {
        yield ['jmalkovich', 'jmalkovich']; // username, username
        yield ['john@malkovich.com', 'jmalkovich']; // email, username
    }
}