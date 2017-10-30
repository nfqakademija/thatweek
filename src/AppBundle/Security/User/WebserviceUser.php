<?php

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class WebserviceUser implements UserInterface, \Serializable
{

    private $id;
    private $username;
    private $firstName;
    private $lastName;
    private $salt;
    private $roles;

    public function __construct()
    {
        return $this;
    }

    public function createFromArray($data)
    {
        $this->username = $data['id'];
        $this->firstName = $data['first_name'];
        $this->lastName = $data['last_name'];
        return $this;
    }

    public function createFromEntity($data)
    {
        $this->id = $data->getId();
        $this->username = $data->getApiId();
        $this->firstName = $data->getFirstName();
        $this->lastName = $data->getLastName();
        return $this;
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function getAccessToken()
    {
        return $this->accessToken;
    }

    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function generateEntity()
    {
        $entity = new User;
        $entity->setApiId($this->username);
        $entity->setFirstName($this->firstName);
        $entity->setLastName($this->lastName);
        return $entity;
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->firstName,
            $this->lastName
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->firstName,
            $this->lastName
            ) = unserialize($serialized);
    }
}