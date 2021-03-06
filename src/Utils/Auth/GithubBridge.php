<?php

namespace App\Utils\Auth;

use App\Vo\Auth\GithubAuthenticateResponseVo;
use App\Vo\Auth\GithubUserResponseVo;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class GithubBridge
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Github
     */
    private $githubService;

    public function __construct(SessionInterface $session, Github $githubService)
    {
        $this->session = $session;
        $this->githubService = $githubService;
    }

    /**
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     */
    public function authorize(): RedirectResponse
    {
        // Generate a random hash and store in the session for security
        $githubAuthState = hash('sha256', microtime(true) . mt_rand());
        $this->session->set('githubAuthState', $githubAuthState);

        $extraParams = [
            'state' => $githubAuthState,
        ];

        $redirectUrl = $this->githubService->authorize($extraParams);

        // Redirect the user to Github's authorization page
        return new RedirectResponse($redirectUrl);
    }

    /**
     * @param string $code
     * @param string $state
     *
     * @throws \RuntimeException
     */
    public function login(string $code, string $state)
    {
        // checks existing (code, state, ...)
        if (empty($code)) {
            throw new BadCredentialsException();
        }
        if (empty($state)) {
            throw new BadCredentialsException();
        }
        if ($state != $this->session->get('githubAuthState')) {
            throw new BadCredentialsException();
        }

        // exchange the auth code for a token
        $githubAuthenticateResponse = $this->authenticate($code, $state);

        // get github user details
        $githubUserDetails = $this->getUserDetails($githubAuthenticateResponse->access_token);

        dump($githubAuthenticateResponse->access_token);
        dump($githubUserDetails);
        //die();

        // create user if not exist (check if no provided email)

        // set user as loggued

    }

    /**
     * @param string $code
     * @param string $state
     *
     * @return GithubAuthenticateResponseVo
     * @throws \RuntimeException
     */
    public function authenticate(string $code, string $state): GithubAuthenticateResponseVo
    {
        $authenticateResponse = $this->githubService->authenticate($code, $state);

        $githubAuthenticateResponse = new GithubAuthenticateResponseVo();
        $githubAuthenticateResponse->populateFromArray($authenticateResponse);

        return $githubAuthenticateResponse;
    }

    /**
     * @param string $accessToken
     *
     * @return GithubUserResponseVo
     * @throws \RuntimeException
     */
    public function getUserDetails(string $accessToken): GithubUserResponseVo
    {
        $userDetails = $this->githubService->getUserDetails($accessToken);

        $githubUserResponseVo = new GithubUserResponseVo();
        $githubUserResponseVo->populateFromArray($userDetails);

        return $githubUserResponseVo;
    }

}
