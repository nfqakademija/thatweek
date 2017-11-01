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
}