<?php

namespace AppBundle\Service;

use AppBundle\Entity\Participant;
use Doctrine\ORM\EntityManager;

class UserHandler
{

    private $em;
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getParticipants($id)
    {
        return $this->em->getRepository(Participant::class)->findByUserId($id);
    }

    public function hydrate($objects)
    {
        $array = array();

        /**
         * @var Participant
         */
        foreach($objects as $object)
        {
            $fields['id'] =  $object->getId();
            $fields['firstName'] = $object->getFirstName();
            $fields['lastName'] = $object->getLastName();
            $fields['age'] = $object->getAge();
            $fields['gender'] = $object->getGender();
            array_push($array, $fields);
        }

        return $array;
    }
}