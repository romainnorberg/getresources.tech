<?php

namespace App\Controller;

use Algolia\SearchBundle\IndexManager;
use App\Entity\Site;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /* @var IndexManager */
    private $indexManager;

    /* @var EntityManager */
    private $em;

    /**
     * DefaultController constructor.
     *
     * @param IndexManager           $indexManager
     * @param EntityManagerInterface $em
     */
    public function __construct(IndexManager $indexManager, EntityManagerInterface $em)
    {
        $this->indexManager = $indexManager;
        $this->em = $em;
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
        $sites = $this->indexManager->rawSearch(
            '', // query
            Site::class, // class
            1, // page
            12, // nb results
            [
                'filters' => 'isValidated=1' // https://www.algolia.com/doc/api-reference/api-parameters/filters/
            ] // parameters
        );

        return $this->render('default/index.html.twig', [
            'sites' => $sites['hits'], // raw search
        ]);


    }
}
