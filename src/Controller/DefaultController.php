<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController
{
    /**
     * @Route("/")
     *
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function index(): Response
    {
        return new Response('Homepage');
    }
}