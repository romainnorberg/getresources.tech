<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationEventListener implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * AuthenticationEventListener constructor.
     *
     * @param ContainerInterface $container
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function __construct(ContainerInterface $container)
    {
        $this->em = $container->get('doctrine')->getManager();
    }

    /**
     * @param Request        $request
     * @param TokenInterface $token
     *
     * @return RedirectResponse|Response
     * @throws \InvalidArgumentException
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token)
    {
        /* @var $user \App\Entity\User */
        $user = $token->getUser();

        // Set last login date
        $user->setLastLogin(new \DateTime());

        $this->em->persist($user);
        $this->em->flush();

        //
        $referer_url = $request->headers->get('referer');

        return new RedirectResponse($referer_url);
    }

}
