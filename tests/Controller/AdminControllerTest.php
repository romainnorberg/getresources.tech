<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;

class AdminControllerTest extends AppWebTestCase
{
    public function testIsSecureArea()
    {
        $url = $this->client->getContainer()->get('router')->generate('admin', []);
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isRedirection());

        // Follow (redirect page)
        $this->client->followRedirect();
        $crawler = $this->client->getCrawler();

        $this->assertTrue($crawler->filter('html:contains("Login")')->count() > 0);
    }
}