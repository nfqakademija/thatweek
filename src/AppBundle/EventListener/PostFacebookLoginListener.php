<?php

namespace AppBundle\EventListener;

use AppBundle\Service\OAuthClient;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class PostFacebookLoginListener
{

    private $em;
    private $client;

    public function __construct(EntityManager $em, OAuthClient $client)
    {
        $this->em = $em;
        $this->client = $client;
    }

    public function onFacebookLogin(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if(!empty($user->getPicture()))
            return;

        if($user)
        {
            $fileName = 'photos/' . md5(uniqid(rand(), true)). '.jpg';
            file_put_contents($fileName, file_get_contents($this->client->getPictureUrl()));
            $user->setPicture($fileName);
            $this->em->persist($user->getEntity());
            $this->em->flush();
        }
    }
}