<?php

namespace App\Controller;

use App\Vo\SiteHitProcessorVo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\Producer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use League\Uri;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class SiteHitController extends AbstractController
{
    /* @var EntityManager */
    private $em;

    /** @var \Enqueue\Client\Producer $producer * */
    private $producer;

    /**
     * DefaultController constructor.
     *
     * @param EntityManagerInterface $em
     * @param Producer               $producer
     */
    public function __construct(EntityManagerInterface $em, Producer $producer)
    {
        $this->em = $em;
        $this->producer = $producer;
    }

    /**
     * @Cache(maxage="0", smaxage="0", public=false, mustRevalidate=true)
     * @Route("/open/{siteSlug}", name="site_hit_open")
     */
    public function index($siteSlug): Response
    {
        $siteRepository = $this->em->getRepository('App:Site');

        // find by slug
        $site = $siteRepository->findOneBy([
            'slug' => $siteSlug,
        ]);

        // check
        // TODO

        // log hit in queue (async)
        // TODO
        $siteHitProcessorVo = new SiteHitProcessorVo();
        $siteHitProcessorVo->uniqId = uniqid('siteHit_', true);
        $this->producer->sendEvent('aSiteHitTopic', $siteHitProcessorVo);

        // generate url
        // TODO: refactor
        $siteUrl = Uri\Http::createFromString($site->getUrl());
        $siteUrl = Uri\merge_query($siteUrl, 'utm_source=getresources.tech');
        $siteUrl = Uri\merge_query($siteUrl, 'utm_medium=site');

        // redirect to site
        return $this->redirect($siteUrl);
    }
}
