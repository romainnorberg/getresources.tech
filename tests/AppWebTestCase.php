<?php

namespace App\Tests;

use Doctrine\ORM\EntityManager;
use Enqueue\Client\TraceableProducer;
use Enqueue\Consumption\QueueConsumer;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AppWebTestCase extends WebTestCase
{
    public const ADMIN_USERNAME = 'jmalkovich';

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

    /**
     * Login user with his username
     *
     * @param string $userName
     * @param array  $sessionParams
     */
    public function logIn($userName = self::ADMIN_USERNAME, array $sessionParams = [])
    {

        $session = $this->client->getContainer()->get('session');

        // the firewall context defaults to the firewall name
        $firewallContext = 'main';

        /* @var $user \App\Entity\User */
        $user = $this->em
            ->getRepository('App:User')
            ->findOneBy([
                'username' => $userName,
            ]);

        // Session
        $token = new UsernamePasswordToken($user, null, $firewallContext, $user->getRoles());
        $session->set('_security_' . $firewallContext, serialize($token));
        foreach ($sessionParams as $name => $value) {
            $session->set($name, $value);
        }
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());

        $this->client->getCookieJar()->set($cookie);
    }
}