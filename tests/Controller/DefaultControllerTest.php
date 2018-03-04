<?php

namespace App\Tests\Controller;

use App\Controller\DefaultController;
use App\Tests\AppWebTestCase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends AppWebTestCase
{
    /**
     * @dataProvider urlProvider
     *
     * @param $url
     */
    public function testPageIsSuccessful($url): void
    {
        $this->client->request('GET', $url);

        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    public function urlProvider()
    {
        yield ['/'];
    }

    /**
     * @covers DefaultController::indexAction()
     *
     * @return Crawler
     */
    public function testHomepage(): Crawler
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