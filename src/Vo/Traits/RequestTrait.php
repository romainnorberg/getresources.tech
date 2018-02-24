<?php

namespace App\Vo\Traits;

use Symfony\Component\HttpFoundation\Request;

trait RequestTrait
{
    public function populateFromRequest(Request $request): void
    {
        if ($clientIp = $request->getClientIp()) {
            $this->ipAddress = $clientIp;
        }

        if ($userAgent = $request->headers->get('User-Agent')) {
            $this->userAgent = $userAgent;
        }
    }
}
