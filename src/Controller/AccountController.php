<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends Controller
{
    /**
     * @Route("/account", name="account")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request): \Symfony\Component\HttpFoundation\Response
    {
        return $this->render(
            'account/index.html.twig'
        );
    }
}