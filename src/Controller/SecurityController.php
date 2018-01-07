<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authUtils
     * @param TranslatorInterface $translator
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function login(AuthenticationUtils $authUtils, TranslatorInterface $translator)
    {
        // get the login error if there is one
        $error = $authUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'page_title'    => $translator->trans('Login page'),
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     * @param Request $request
     */
    public function logout(Request $request)
    {

    }

    /**
     * @Route("/recover_password", name="user_recover_password")
     * @param Request $request
     */
    public function recoverPassword(Request $request)
    {
        return $this->render('security/recover_password.html.twig');
    }
}