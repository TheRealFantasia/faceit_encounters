<?php

namespace App\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class FaceitProvider extends AbstractProvider
{

    public function getBaseAuthorizationUrl()
    {
        return 'https://cdn.faceit.com/widgets/sso/index.html';
    }

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://api.faceit.com/auth/v1/oauth/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return 'https://api.faceit.com/auth/v1/resources/userinfo';
    }

    protected function getDefaultScopes()
    {
        return ['openid', 'profile'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        //TODO
        return;
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new FaceitUser($response);
    }

    public function getHeaders($token = null)
    {
        return array_merge(
            $this->getDefaultHeaders(),
            $this->getAuthorizationHeaders($token)
        );
    }

    protected function getAuthorizationHeaders($token = null)
    {
        if ($token === null) {
            return [
                'Authorization' => $this->getBasicAuthorization()
            ];
        }
        return [
            'Authorization' => 'Bearer ' . $token->getToken()
        ];
    }

    private function getBasicAuthorization(): string
    {
        return 'Basic ' . base64_encode($this->clientId . ':' . $this->clientSecret);
    }
}
