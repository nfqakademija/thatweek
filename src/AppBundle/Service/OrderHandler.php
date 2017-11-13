<?php

namespace AppBundle\Service;

use AppBundle\Entity\Participant;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Order;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Day;

class OrderHandler
{

    private $em;
    private $dayHandler;

    public function __construct(EntityManager $em, DayHandler $dayHandler)
    {
        $this->em = $em;
        $this->dayHandler = $dayHandler;
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     * @param User $user
     * @param Order $order
     * @return bool
     */
    public function handle(Request $request, FormInterface $form, User $user, Order $order)
    {

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $participantList = $form['participants']->getData();

            $startDate = $form['startDate']->getData();
            $endDate = $form['endDate']->getData();
            $this->updateDays($order, $startDate, $endDate);
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
        foreach ($entities as $entity) {
            $order->getParticipants()->add($entity);
        }
        return $order;
    }

    /**
     * @param $order Order
     * @param $startTimestamp integer
     * @param $endTimestamp integer
     */
    private function updateDays($order, $startTimestamp, $endTimestamp)
    {
        $foundDays = $this->getDaysBetweenTimestamps($startTimestamp, $endTimestamp);
        if ($foundDays != null) {
            /**
             * @var $foundDay Day
             */
            foreach ($foundDays as $foundDay) {
                $foundDay->addOrder($order);
            }
        }
    }

    /**
     * @param $startTimestamp integer
     * @param $endTimestamp integer
     * @return array
     */
    public function getDaysWithOrders($startTimestamp, $endTimestamp)
    {
        $results = $this->getDaysBetweenTimestamps($startTimestamp, $endTimestamp);

        $days = array();
        /**
         * @var $result Day
         */
        foreach ($results as $result) {
            $day = ['orderCount' => $result->getOrders()->count(),
                'capacity' => $result->getCapacity(),
                'date' => $result->getDate()->getTimestamp()];
            array_push($days, $day);
        }

        return $days;
    }

    /**
     * @param $startTimestamp integer
     * @param $endTimestamp integer
     * @return mixed
     */
    private function getDaysBetweenTimestamps($startTimestamp, $endTimestamp)
    {
        $startDate = $this->dayHandler->timestampToDate($startTimestamp);
        $endDate = $this->dayHandler->timestampToDate($endTimestamp);

        return $this->dayHandler->getDaysBetweenDates($startDate, $endDate);
    }
}