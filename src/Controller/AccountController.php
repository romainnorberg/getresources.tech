<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\Type\SiteSubmitType;
use App\Vo\UserSubmitSiteVo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Enqueue\Client\Producer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class AccountController extends Controller
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
    public function __construct(EntityManagerInterface $em, $producer)
    {
        $this->em = $em;
        $this->producer = $producer;
    }

    /**
     * @Route("/account", name="account")
     * @param Request $request
     * @Cache(maxage="0", smaxage="0", public=false, mustRevalidate=true)
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'account/index.html.twig'
        );
    }

    /**
     * @Route("/submit", name="submit")
     * @param Request $request
     *
     * @Cache(maxage="0", smaxage="0", public=true)
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function submitAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $site = new Site();
        $form = $this->createForm(SiteSubmitType::class, $site);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $site = new Site();
            $site->setUrl($data->getUrl());
            $site->setName($data->getName());
            $site->setDescription($data->getDescription());
            $site->setCreatedBy($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($site);
            $em->flush();

            // Set flash message
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->add('success', 'Thank for submit');

            // Send thank mail (async)
            $UserSubmitSiteVo = new UserSubmitSiteVo();
            $UserSubmitSiteVo->uniqId = uniqid('userSubmitSite_', true);

            $this->producer->sendEvent('aUserSubmitSiteTopic', $UserSubmitSiteVo);

            // Redirect
            return $this->redirectToRoute('homepage', []);
        }

        return $this->render(
            'account/submit.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }
}