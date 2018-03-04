<?php

namespace App\Controller;

use App\Form\Type\UserType;
use App\Entity\User;
use App\Vo\UserRegistrationProcessorVo;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Enqueue\Client\Message;
use Enqueue\Client\Producer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\TranslatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;

class RegistrationController extends Controller
{
    /* @var EntityManager */
    private $em;

    /** @var \Enqueue\Client\Producer $producer */
    private $producer;

    /**
     * DefaultController constructor.
     *
     * @param EntityManagerInterface $em
     * @param Producer               $producer
     */
    public function __construct(EntityManagerInterface $em, $producer)
    {
        $this->em = $em;
        $this->producer = $producer;
    }

    /**
     * @Route("/register", name="user_registration")
     * @param Request             $request
     * @param TranslatorInterface $translator
     *
     * @Cache(maxage="0", smaxage="0", public=false, mustRevalidate=true)
     * @throws \Symfony\Component\Form\Exception\LogicException
     * @throws \LogicException
     * @throws \Symfony\Component\Translation\Exception\InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \InvalidArgumentException
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function registerAction(Request $request, TranslatorInterface $translator)
    {
        // build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword($user->getPlainPassword());
            $user->setCreated(new \DateTime());
            $user->setUpdated(new \DateTime());

            // save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // send email (async)
            // TODO
            $UserRegistrationProcessorVo = new UserRegistrationProcessorVo();
            $UserRegistrationProcessorVo->uniqId = uniqid('userRegistration_', true);

            $this->producer->sendEvent('aUserRegistrationTopic', $UserRegistrationProcessorVo);

            // set flash
            $flashbag = $this->get('session')->getFlashBag();
            $flashbag->add('success', 'Thank for registration');

            // login user
            $providerKey = 'secured_area';
            $token = new UsernamePasswordToken($user, null, $providerKey, $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);

            return $this->redirectToRoute('homepage');
        }

        return $this->render(
            'registration/register.html.twig',
            [
                'page_title' => $translator->trans('Registration page'),
                'form'       => $form->createView(),
                'error'      => null,
            ]
        );
    }
}