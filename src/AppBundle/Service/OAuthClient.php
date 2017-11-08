<?php

namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\Session\Session;

class OAuthClient
{

    private $provider;
    private $accessToken;
    private $fieldsToRetrieve = array('first_name', 'last_name', 'id', 'picture.type(large)');

    public function __construct($facebook_data)
    {
        $this->provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => $facebook_data['client_id'],
            'clientSecret' => $facebook_data['client_secret'],
            'redirectUri' => $facebook_data['redirect_uri'],
            'urlAuthorize' => $facebook_data['url_authorize'],
            'urlAccessToken' => $facebook_data['url_access_token'],
            'urlResourceOwnerDetails' => $this->formResourceUrl($facebook_data['url_resource'])]);
    }

    public function connect(Request $request)
    {
        $authorizationUrl = $this->provider->getAuthorizationUrl();

        if($request->hasSession())
            $session = $request->getSession();
        else $session = new Session();

        $session->set('oauth2state', $this->provider->getState());
        header('Location: ' . $authorizationUrl);

    }

    public function generateAccessToken(Request $request)
    {
        $session = $request->getSession();
        $state = $request->get('state');
        if(empty($state) || $session->has('oauth2state') && $state !== $session->get('oauth2state'))
        {
            $session->remove('oauth2state');

            throw new \Exception('Invalid state');
        }
        try {
            $this->accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $request->get('code')
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

    private function formResourceUrl($url)
    {
        return $url . implode(',', $this->fieldsToRetrieve);
    }

    public function getPictureUrl()
    {
        return $this->getUser($this->accessToken)['picture']['data']['url'];
    }

}