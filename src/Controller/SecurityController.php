<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use App\Utils\Auth\GithubBridge;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authUtils
     * @param TranslatorInterface $translator
     *
     * @Cache(maxage="0", smaxage="0", public=false, mustRevalidate=true)
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \Symfony\Component\Routing\Exception\RouteNotFoundException
     * @throws \Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException
     * @throws \Symfony\Component\Routing\Exception\MissingMandatoryParametersException
     * @throws \Symfony\Component\Routing\Exception\InvalidParameterException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     */
    public function login(AuthenticationUtils $authUtils, TranslatorInterface $translator): \Symfony\Component\HttpFoundation\Response
    {
        if (true === $this->get('security.authorization_checker')->isGranted('ROLE_USER')) {
            $url = $this->container->get('router')->generate('homepage');

            return new RedirectResponse($url);
        }

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
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recoverPassword(Request $request)
    {
        return $this->render('security/recover_password.html.twig');
    }

    /**
     * @Route("/auth/github", name="auth_github")
     *
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function authWithGithub(): RedirectResponse
    {
        return $this->get(GithubBridge::class)->authorize();
    }

    /**
     * @Route("/auth/github/callback", name="auth_github_callback")
     * @param Request $request
     *
     * @throws \RuntimeException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function authWithGithubCallback(Request $request)
    {
        $code = $request->get('code');
        $state = $request->get('state');

        if (empty($code)) {
            throw $this->createNotFoundException();
        }

        if (empty($state)) {
            throw $this->createNotFoundException();
        }

        $this->get(GithubBridge::class)->login($code, $state);


        die();
    }
}