<?php

namespace App\Tests\Controller;

use App\Controller\SiteHitController;
use App\Repository\SiteRepository;
use App\Tests\AppWebTestCase;
use App\Vo\SiteHitProcessorVo;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SiteHitControllerTest extends AppWebTestCase
{
    /* @var $siteRepository SiteRepository */
    private $siteRepository;

    public function setUp()
    {
        //
        parent::setUp();

        // Repos
        $this->siteRepository = $this->em->getRepository('App:Site');
    }

    /**
     * @covers       SiteHitController::indexAction
     *
     * @dataProvider siteProvider()
     *
     * @param $siteSlug
     * @param $headers
     *
     * @throws \Exception
     */
    public function testValidClick($siteSlug, array $headers = null): void
    {
        // Headers
        foreach ($headers as $key => $value) {
            $this->client->setServerParameter($key, $value);
        }

        // Find by slug
        /* @var $site \App\Entity\Site */
        $site = $this->siteRepository->findOneBy([
            'slug' => $siteSlug,
        ]);

        // Url
        $url = $this->client->getContainer()->get('router')->generate(
            'site_hit_open',
            [
                'siteSlug' => $site->getSlug(),
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->client->request('GET', $url);

        // Assert redirection
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals($site->getUtils()->getUrlWithExtra(), $this->client->getResponse()->headers->get('location')); // check url (with extra query)

        // Assert site hit processor
        // Queue
        $traces = $this->getProducer()->getTopicTraces('aSiteHitTopic');

        $this->assertCount(1, $traces);
        $this->assertEquals('aSiteHitTopic', $traces[0]['topic']);
        $this->assertInstanceOf(SiteHitProcessorVo::class, $traces[0]['body']);

        // Consume queue
        // TODO

        // Check values
        // TODO

    }

    public function siteProvider(): array
    {
        return [
            ['javascript-es6-var-let-or-const', [
                'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/63.0.3239.84 Safari/537.36',
                'REMOTE_ADDR'     => '149.154.246.81',
            ]],
        ];
    }
}