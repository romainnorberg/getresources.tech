<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class ResponseListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => [['onKernelResponse', -256]],
        ];
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $encodings = $request->getEncodings();

        // Compression
        if (\in_array('gzip', $encodings, true) && \function_exists('gzencode')) {
            $content = gzencode($response->getContent());
            $response->setContent($content);
            $response->headers->set('Content-encoding', 'gzip');
        } elseif (\in_array('deflate', $encodings, true) && \function_exists('gzdeflate')) {
            $content = gzdeflate($response->getContent());
            $response->setContent($content);
            $response->headers->set('Content-encoding', 'deflate');
        }

        // Cache control
        $response->setPublic();
        $response->setMaxAge(600);
        $response->setSharedMaxAge(600);
        $response->headers->addCacheControlDirective('must-revalidate');
    }
}