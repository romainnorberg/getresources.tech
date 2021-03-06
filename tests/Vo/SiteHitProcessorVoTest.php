<?php

namespace App\Tests\Vo;

use App\Vo\SiteHitProcessorVo;
use PHPUnit\Framework\TestCase;

/**
 * Class SiteHitProcessorVoTest
 * @package App\Tests\Vo
 *
 * @covers  \App\Vo\SiteHitProcessorVo
 */
class SiteHitProcessorVoTest extends TestCase
{
    /**
     * @covers \App\Vo\SiteHitProcessorVo::__construct
     */
    public function testConstructor(): void
    {
        $siteHitProcessorVo = new SiteHitProcessorVo();

        $this->assertStringStartsWith('siteHit_', $siteHitProcessorVo->uniqId);
        $this->assertInstanceOf(\DateTimeImmutable::class, $siteHitProcessorVo->created);
    }

    /**
     * @dataProvider dataProperties()
     *
     * @param string $property
     */
    public function testProperties(string $property): void
    {
        $this->assertClassHasAttribute($property, SiteHitProcessorVo::class, "Class SiteHitProcessorVo has '$property' property.");
    }

    public function dataProperties(): array
    {
        return [
            ['uniqId'],
            ['siteId'],
            ['userId'],
            ['userAgent'],
            ['ipAddress'],
            ['created'],
        ];
    }
}