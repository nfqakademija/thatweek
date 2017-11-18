<?php

namespace AppBundle\Service;

use AppBundle\Entity\Participant;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use Symfony\Component\Config\Definition\Exception\Exception;

class UserHandler
{

    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function findUser($id)
    {
        return $this->em->getRepository(User::class)->findOneBy(array('id' => $id));
    }

    public function getParticipants($id)
    {
        return $this->em->getRepository(Participant::class)->findByUserId($id);
    }

    /**
     * @param $user User
     */
    public function getOrders($user)
    {
        return $user->getOrders();
    }

    public function participantsToArray($objects)
    {
        $array = array();
        /**
         * @var $object Participant
         */
        foreach($objects as $object)
        {
            $fields['id'] = $object->getId();
            $fields['firstName'] = $object->getFirstName();
            $fields['lastName'] = $object->getLastName();
            $fields['age'] = $object->getAge();
            $fields['gender'] = $object->getGender();
            array_push($array, $fields);
        }

        return $array;
    }
}