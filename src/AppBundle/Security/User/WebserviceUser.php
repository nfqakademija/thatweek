<?php

namespace AppBundle\Security\User;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

class WebserviceUser implements UserInterface, \Serializable
{

    /*private $id;
    private $username;
    private $firstName;
    private $lastName;
    private $salt;*/
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

   /* public function createFromArray($data)
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
    }*/

    public function getRoles()
    {
        return array('ROLE_USER');
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

    public function getPassword()
    {

    }

    public function eraseCredentials()
    {

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