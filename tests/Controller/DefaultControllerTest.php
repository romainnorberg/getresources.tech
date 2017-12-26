<?php

namespace App\Tests;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @var \Symfony\Component\HttpKernel\Client
     */
    protected $client;

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setUp()
    {
        $this->client = $client = self::createClient();
        $this->container = $this->client->getContainer();
    }

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
}