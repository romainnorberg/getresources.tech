<?php

namespace App\Tests;

use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Dotenv\Dotenv;

class AppWebTestCase extends WebTestCase
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
        $this->client = self::createClient();
        $this->container = $this->client->getContainer();
    }

    protected static function createClient(array $options = [], array $server = [])
    {
        if (!class_exists(Dotenv::class)) {
            throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
        }
        (new Dotenv())->load(__DIR__ . '/../.env.test');

        return parent::createClient($options, $server);
    }
}