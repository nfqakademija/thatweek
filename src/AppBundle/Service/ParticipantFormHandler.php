<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormInterface;
use AppBundle\Entity\Participant;
use AppBundle\Form\ParticipantType;

class ParticipantFormHandler
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(Request $request,FormInterface $form, $userId)
    {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $participant = $form->getData();
            $participant->setUserId($userId);

            $this->em->persist($participant);
            $this->em->flush();
            return true;
        }

        return false;
    }
}