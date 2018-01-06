<?php

namespace App\Controller;

use Algolia\SearchBundle\IndexManager;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    /**
     * @Route("/", name="homepage")
     *
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function index(): Response
    {
        $em = $this->getDoctrine()->getManager();

        $sites = $this->indexManager->search('example', Site::class, $em);

        return $this->render('default/index.html.twig', [
            'sites' => $sites,
        ]);
    }
}