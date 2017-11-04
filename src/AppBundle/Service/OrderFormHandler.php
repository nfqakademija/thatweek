<?php

namespace AppBundle\Service;

use AppBundle\Entity\Participant;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Order;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Week;

class OrderFormHandler
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(Request $request,FormInterface $form, User $user, Order $order)
    {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $participantList = $form['participants']->getData();
            $weekId = $form['week']->getData();

            $order = $this->setWeek($weekId, $order);
            $order = $this->setParticipants($participantList, $order);
            $order->setUser($user);
            $this->em->persist($order);
            $this->em->flush();
            return true;
        }

        return false;
    }

    /**
     * @param $participants string
     * @return mixed Participants
     */
    private function getEntities($participantList)
    {
        $participantsIds = explode(',', $participantList);
        $entities = $this->em->getRepository(Participant::class)->findBy(array('id' => $participantsIds));
        return $entities;
    }

    /**
     * @param $participantList string
     * @param $order Order
     * @return Order
     */
    private function setParticipants($participantList, $order)
    {
        $entities = $this->getEntities($participantList);
        foreach($entities as $entity)
        {
            $order->getParticipants()->add($entity);
        }
        return $order;
    }

    /**
     * @param $weekId
     * @param $order
     * @return Order
     */
    private function setWeek($weekId, $order)
    {
        $week = $this->em->getRepository(Week::class)->find($weekId);
        $order->setWeek($week);
        return $order;
    }
}