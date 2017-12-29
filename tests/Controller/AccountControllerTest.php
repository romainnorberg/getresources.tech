<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;

class AccountControllerTest extends AppWebTestCase
{
    public function testIsSecureArea()
    {
        $url = $this->client->getContainer()->get('router')->generate('account', []);
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        // Follow (redirect page)
        $this->client->followRedirect();
        $crawler = $this->client->getCrawler();

        $this->assertTrue($crawler->filter('html:contains("Login")')->count() > 0);
    }
}