<?php

namespace App\Tests\Controller;

use App\Tests\AppWebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends AppWebTestCase
{
    /**
     * @dataProvider urlProvider
     *
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/'];
    }

    public function testHomepage()
    {
        $url = $this->client->getContainer()->get('router')->generate('homepage', []);
        $crawler = $this->client->request('GET', $url);

        $response = $this->client->getResponse();

        // Form
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('homepage', $crawler->filterXPath('//body')->attr('class')); // mandatory for JS
        $this->assertContains('Find all tech resources, search and filter by language, type and more.', $response->getContent());

        return $crawler;
    }
}