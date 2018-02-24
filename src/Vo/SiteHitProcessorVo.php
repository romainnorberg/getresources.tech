<?php

namespace App\Vo;

use App\Vo\Traits\RequestTrait;

class SiteHitProcessorVo extends ValueObject
{
    use RequestTrait;

    public function __construct()
    {
        $this->uniqId = uniqid('siteHit_', true);
        $this->created = new \DateTimeImmutable();
    }

    public $uniqId;
    public $siteId;
    public $userId;
    public $userAgent;
    public $ipAddress;
    public $created;
}
