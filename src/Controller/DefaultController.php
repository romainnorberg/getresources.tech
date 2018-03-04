<?php

namespace App\Controller;

use Algolia\SearchBundle\IndexManager;
use App\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class DefaultController extends AbstractController
{
    /* @var IndexManager */
    private $indexManager;

    /**
     * DefaultController constructor.
     *
     * @param IndexManager $indexManager
     */
    public function __construct(IndexManager $indexManager)
    {
        $this->indexManager = $indexManager;
    }

    /**
     * @Route("/", name="homepage")
     *
     * @Cache(maxage="0", smaxage="0", public=false, mustRevalidate=true)
     * @return Response
     * @throws \LogicException
     * @throws \InvalidArgumentException
     */
    public function indexAction(): Response
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
