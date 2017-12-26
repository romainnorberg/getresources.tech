<?php

namespace App\Tests;

use App\EventListener\ResponseListener;
use App\Kernel;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

class ResponseListenerTest extends TestCase
{
    private $dispatcher;

    private $kernel;

    protected function setUp()
    {
        $this->kernel = new Kernel('prod', true); # simulate production
        $this->dispatcher = new EventDispatcher();
        $listener = new ResponseListener($this->kernel);
        $this->dispatcher->addListener(KernelEvents::RESPONSE, [$listener, 'onKernelResponse']);

    }

    protected function tearDown()
    {
        $this->dispatcher = null;
        $this->kernel = null;
    }

    /**
     * @dataProvider urlProvider
     *
     * @covers       ResponseListener::onKernelResponse
     *
     * @param string $value
     * @param        $expected
     */
    public function testResponseEncoding($value, $expected)
    {
        $listener = new ResponseListener($this->kernel);
        $this->dispatcher->addListener(KernelEvents::RESPONSE, [$listener, 'onKernelResponse'], 1);

        $response = new Response('foo');
        $server = [
            'HTTP_ACCEPT_ENCODING' => $value,
        ];
        $request = Request::create('/', $method = 'GET', $parameters = [], $cookies = [], $files = [], $server, $content = null);

        $event = new FilterResponseEvent($this->kernel, $request, HttpKernelInterface::MASTER_REQUEST, $response);
        $this->dispatcher->dispatch(KernelEvents::RESPONSE, $event);

        $this->assertEquals($expected, $response->headers->get('Content-encoding'));
    }

    public function urlProvider()
    {
        yield 'empty' => [
            null,
            null,
        ];

        yield 'gzip' => [
            'gzip, deflate, br',
            'gzip',
        ];

        yield 'deflate' => [
            'deflate',
            'deflate',
        ];
    }
}