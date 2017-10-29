<?php

namespace AppBundle\Security\User;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use AppBundle\Utils\OAuthClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class Authenticator extends AbstractGuardAuthenticator
{
    private $client;

    public function __construct(OAuthClient $client)
    {
        $this->client = $client;
    }

    public function getCredentials(Request $request)
    {
        if ($request->getPathInfo() === '/login/facebook/check') {
            $this->client->generateAccessToken();
        }

        return $this->client->getAccessToken();
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return true;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {

        $data = array(
        'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

    );

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return "required";
    }

    public function supportsRememberMe()
    {
        return false;
    }

}