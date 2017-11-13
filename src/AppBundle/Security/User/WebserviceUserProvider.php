<?php

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use AppBundle\Security\User\WebserviceUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityManager;
use AppBundle\Service\OAuthClient;

class WebserviceUserProvider implements UserProviderInterface
{
    private $em;
    private $client;

    public function __construct(EntityManager $em, OAuthClient $client)
    {
        $this->em = $em;
        $this->client = $client;
    }

    /**
     * @param string $token
     * @return \AppBundle\Security\User\WebserviceUser
     */
    public function loadUserByUsername($token)
    {
        $userData = $this->client->getUser($token);
        /**
         * @var User
         */
        $user = $this->getUser($userData['id']);
        if($user === NULL) {
           $user = $this->createUser($userData);
        }

        $webUser = new WebserviceUser($user);
        return $webUser;
    }

    /**
     * @param $userData
     * @return User
     */
    private function createUser($userData)
    {
        $user = new User();
        $user->setApiId($userData['id'])
            ->setFirstName($userData['first_name'])
            ->setLastName($userData['last_name'])
            ->addRole('ROLE_USER');
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    public function loadUserById($id)
    {
        return new WebserviceUser($this->getUser($id));
    }

    /**
     * @param $id
     * @return null|User
     */
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