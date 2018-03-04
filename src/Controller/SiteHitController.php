<?php

namespace App\Controller;

use App\Vo\SiteHitProcessorVo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\Producer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class SiteHitController extends AbstractController
{
    /* @var EntityManager */
    private $em;

    /** @var \Enqueue\Client\Producer $producer */
    private $producer;

    /**
     * DefaultController constructor.
     *
     * @param EntityManagerInterface $em
     * @param Producer               $producer
     */
    public function __construct(EntityManagerInterface $em, $producer)
    {
        $this->em = $em;
        $this->producer = $producer;
    }

    /**
     * @Cache(maxage="0", smaxage="0", public=false, mustRevalidate=true)
     * @Route("/open/{siteSlug}", name="site_hit_open")
     * @param         $siteSlug
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|NotFoundHttpException
     * @throws \LogicException
     */
    public function indexAction($siteSlug, Request $request)
    {
        $siteRepository = $this->em->getRepository('App:Site');

        // find by slug
        /* @var $site \App\Entity\Site */
        $site = $siteRepository->findOneBy([
            'slug' => $siteSlug,
        ]);

        // check
        if (null === $site) {
            return new NotFoundHttpException('Site not found');
        }

        // log hit in queue (async)
        $siteHitProcessorVo = new SiteHitProcessorVo();
        $siteHitProcessorVo->populateFromRequest($request);
        $siteHitProcessorVo->siteId = $site->getId();
        $siteHitProcessorVo->userId = $this->getUser() ? $this->getUser()->getId() : null;
        $this->producer->sendEvent('aSiteHitTopic', $siteHitProcessorVo);

        // generate url
        // TODO: refactor
        $siteUrl = $site->getUtils()->getUrlWithExtra();

        // redirect to site
        return $this->redirect($siteUrl);
    }
}
