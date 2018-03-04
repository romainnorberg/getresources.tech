<?php

namespace App\Utils\Auth;

use GuzzleHttp\Client;

class Github
{
    public $authorizeURL = 'https://github.com/login/oauth/authorize'; // GET
    public $tokenURL = 'https://github.com/login/oauth/access_token'; // POST
    public $apiURLBase = 'https://api.github.com/';
    public $apiURLUser = 'https://api.github.com/user';
    public $defaultUserScope = 'read:user';
    public $defaultUserAgent = 'getresources.tech (dev)';

    private $authClientId;
    private $authClientSecret;
    private $authCallbackUrl;

    public function __construct()
    {
        $this->authClientId = $_ENV['AUTH_GITHUB_CLIENT_ID'];
        $this->authClientSecret = $_ENV['AUTH_GITHUB_CLIENT_SECRET'];
        $this->authCallbackUrl = $_ENV['AUTH_GITHUB_CALLBACK_URL_PROTOCOL'] . $_ENV['AUTH_GITHUB_CALLBACK_URL'];
    }

    /**
     *
     *
     * Doc: https://developer.github.com/apps/building-oauth-apps/authorization-options-for-oauth-apps/
     *
     * @param array|null $extraParams
     *
     * @return string
     */
    public function authorize(array $extraParams = null): string
    {
        $params = [
            'client_id'    => $this->authClientId,
            'redirect_uri' => $this->authCallbackUrl,
            'scope'        => $this->defaultUserScope,
        ];

        $params = array_merge($params, $extraParams);

        // return url to redirect the user to Github's authorization page
        return $this->authorizeURL . '?' . http_build_query($params);
    }

    /**
     *
     *
     * Doc: https://developer.github.com/apps/building-oauth-apps/authorization-options-for-oauth-apps/
     *
     * @param string $code
     * @param string $state
     *
     * @return array
     * @throws \RuntimeException
     */
    public function authenticate(string $code, string $state): array
    {
        $client = new Client();
        $formParams = [
            'client_id'     => $this->authClientId,
            'client_secret' => $this->authClientSecret,
            'code'          => $code,
            'redirect_uri'  => $this->authCallbackUrl,
            'state'         => $state,
        ];

        $res = $client->request('POST', $this->tokenURL, [
            'form_params' => $formParams,
            'headers'     => [
                'Accept'     => 'application/json',
                'User-Agent' => $this->defaultUserAgent,
            ],
        ]);

        return \json_decode($res->getBody()->getContents(), true);
    }

    /**
     * Doc: https://developer.github.com/v3/users/#get-the-authenticated-user
     *
     * @param string $accessToken
     *
     * @return array
     * @throws \RuntimeException
     */
    public function getUserDetails(string $accessToken): array
    {
        $client = new Client();

        $headers = [
            'Authorization' => 'Bearer ' . $accessToken,
            'User-Agent'    => $this->defaultUserAgent,
            'Accept'        => 'application/json',
            'Cache-Control' => 'no-cache',
        ];

        $res = $client->request('GET', $this->apiURLUser, [
            'headers' => $headers,
        ]);

        return \json_decode($res->getBody()->getContents(), true);
    }
}