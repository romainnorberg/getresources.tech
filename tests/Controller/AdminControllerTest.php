<?php

namespace App\Tests\Controller;

use App\Controller\AdminController;
use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends AppWebTestCase
{
    /**
     * @covers AdminController::indexAction()
     *
     * @throws \Exception
     */
    public function testIsSecureArea(): void
    {
        $url = $this->client->getContainer()->get('router')->generate('admin', []);
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // Asserts
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertContains('Login', $response->getContent()); // header
    }

    /**
     * @covers AdminController::indexAction()
     *
     * @throws \Exception
     */
    public function testIsSecureAreaWithRoleSuperAdmin(): void
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
     * @covers AdminController::indexAction()
     *
     * @throws \Exception
     */
    public function testIsSuperSecureAreaWithRoleAdmin(): void
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