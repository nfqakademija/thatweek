<?php

namespace AppBundle\Utils;

use AppBundle\Security\User\WebserviceUserProvider;
use League\OAuth2\Client\Token\AccessToken;

class OAuthClient
{

    private $provider;
    private $accessToken;
    public $calls;
    public function __construct()
    {
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => '1902721706722915',
            'clientSecret' => 'ecacaf772f3588c8579ef3e46d2b4184',
            'redirectUri' => 'http://localhost:8000/login/facebook/check',
            'urlAuthorize' => 'https://www.facebook.com/v2.10/dialog/oauth?',
            'urlAccessToken' => 'https://graph.facebook.com/v2.10/oauth/access_token?',
            'urlResourceOwnerDetails' => 'https://graph.facebook.com/me?fields=first_name,last_name,id']);
    }

    public function connect()
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $this->provider->getState();
        header('Location: ' . $authorizationUrl);

    }

    public function generateAccessToken()
    {
        if (empty($_GET['state']) || (isset($_SESSION['oauth2state']) && $_GET['state'] !== $_SESSION['oauth2state'])) {

            if (isset($_SESSION['oauth2state'])) {
                unset($_SESSION['oauth2state']);
            }

            exit('Invalid state');

        }
        try {
            $this->accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {

            exit($e->getMessage());

        }
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getUser($accessToken)
    {
        return $this->provider->getResourceOwner($accessToken)->toArray();
    }

}