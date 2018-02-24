<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Enqueue\Client\TraceableProducer;
use Enqueue\Consumption\QueueConsumer;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Dotenv\Dotenv;

class AppWebTestCase extends WebTestCase
{
    /**
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var QueueConsumer
     */
    protected $consumer;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager('default');

        $this->client = static::createClient();
        $this->consumer = static::$kernel->getContainer()->get('enqueue.consumption.queue_consumer');
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

    /**
     * @return TraceableProducer
     */
    public function getProducer()
    {
        return $this->client->getContainer()->get('enqueue.producer');
    }
}