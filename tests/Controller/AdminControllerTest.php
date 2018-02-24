<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends AppWebTestCase
{
    /**
     * @throws \Exception
     */
    public function testIsSecureArea()
    {
        $url = $this->client->getContainer()->get('router')->generate('admin', []);
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // Asserts
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertContains('Login', $response->getContent()); // header
    }

    /**
     * @throws \Exception
     */
    public function testIsSecureAreaWithRoleSuperAdmin()
    {
        $this->logIn();

        $url = $this->client->getContainer()->get('router')->generate('admin', []);
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // Assert redirection
        $this->assertTrue($response->isRedirection());
        $this->assertRegExp('/^\/admin\/admin/', $response->headers->get('location'));

        // Redirect
        $this->client->followRedirect();
        $response = $this->client->getResponse();
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
    }

    /**
     * @throws \Exception
     */
    public function testIsSuperSecureAreaWithRoleAdmin()
    {
        $this->logIn('obins_admin'); // user with role 'ROLE_ADMIN' (not super)

        $url = $this->client->getContainer()->get('router')->generate('easyadmin', [
            'entity' => 'User', // Secure with role: ROLE_SUPER_ADMIN
            'action' => 'list',
        ]);
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // Asserts
        $this->assertSame(Response::HTTP_FORBIDDEN, $response->getStatusCode());
        $this->assertContains('Access Denied', $response->getContent()); // header
    }
}