<?php

namespace App\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminController extends EasyAdminController
{
    public const USER_ENTITY = 'User';

    /**
     * @Route("/admin", name="easyadmin")
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function indexAction(Request $request)
    {
        // check user role to manage Users
        if ($request->get('entity') === self::USER_ENTITY) {
            $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        }

        return parent::indexAction($request);
    }
}