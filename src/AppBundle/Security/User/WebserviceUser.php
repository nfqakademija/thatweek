<?php

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class WebserviceUser implements UserInterface, \Serializable
{

    /**
     * @var User
     */
    private $entity;

    /**
     * WebserviceUser constructor.
     * @param $entity User
     */
    public function __construct(User $entity)
    {
        $this->entity = $entity;
    }

    public function getRoles()
    {
        return $this->entity->getRoles();
    }

    /**
     * @return User
     */
    public function getEntity()
    {
        return $this->entity;
    }

    public function getId()
    {
        return $this->entity->getId();
    }

    public function getUsername()
    {
        return $this->entity->getApiId();
    }

    public function getSalt()
    {

    }

    public function getFirstName()
    {
        return $this->entity->getFirstName();
    }

    public function getLastName()
    {
        return $this->entity->getLastName();
    }

    public function setPicture($picture)
    {
        $this->entity->setPicture($picture);
        return $this;
    }

    public function getPicture()
    {
        return $this->entity->getPicture();
    }

    public function getPassword()
    {

    }

    public function eraseCredentials()
    {

    }

    public function serialize()
    {
        return serialize(array(
            $this->entity
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->entity
            ) = unserialize($serialized);
    }
}