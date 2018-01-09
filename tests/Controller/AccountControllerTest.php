<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class AccountControllerTest extends AppWebTestCase
{
    public function testIsSecureArea()
    {
        $url = $this->client->getContainer()->get('router')->generate('account', []);
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();

        // forwarded
        $this->assertSame(Response::HTTP_UNAUTHORIZED, $response->getStatusCode());
        $this->assertContains('Login', $response->getContent()); // header
    }
}