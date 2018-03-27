<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationEventListener implements AuthenticationSuccessHandlerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AuthenticationEventListener constructor.
     *
     * @param EntityManagerInterface $em
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
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

        $referer_url = $request->headers->get('referer');

        return new RedirectResponse((string)$referer_url);
    }

}
