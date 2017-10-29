<?php

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use AppBundle\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManager;
use AppBundle\Utils\OAuthClient;

class WebserviceUserProvider implements UserProviderInterface
{
    private $em;
    private $client;

    public function __construct(EntityManager $em, OAuthClient $client)
    {
        $this->em = $em;
        $this->client = $client;
    }

    public function loadUserByUsername($token)
    {
        $userData = $this->client->getUser($token);
        $user = new WebserviceUser();
        $user->createFromArray($userData);
        if(!$this->userExists($user))
        {
            $this->createUser($user);
        }

        return $user;
    }

    private function createUser($user)
    {
        $this->em->persist($user->generateEntity());
        $this->em->flush();
    }

    private function userExists($user)
    {
        $results = $this->getUser($user->getUsername());
        if($results === NULL)
            return false;
        return true;
    }

    public function loadUserById($id)
    {
        $user = new WebserviceUser();
        return $user->createFromEntity($this->getUser($id));
    }


    private function getUser($id)
    {
        return $this->em->getRepository(User::class)->findOneBy(array('apiId' => $id));
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof WebserviceUser) {
        throw new UnsupportedUserException(
        sprintf('Instances of "%s" are not supported.', get_class($user))
        );
        }

        return $this->loadUserById($user->getUsername());
    }

    public function supportsClass($class)
    {
        return WebserviceUser::class === $class;
    }
}