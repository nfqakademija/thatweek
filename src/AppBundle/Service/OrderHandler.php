<?php

namespace AppBundle\Service;

use AppBundle\Entity\Participant;
use MongoDB\Driver\Exception\ExecutionTimeoutException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Order;
use Doctrine\ORM\EntityManager;
use AppBundle\Entity\User;
use AppBundle\Entity\Day;

class OrderHandler
{

    protected $em;
    protected $dayHandler;
    protected $userHandler;

    public function __construct(EntityManager $em, DayHandler $dayHandler, UserHandler $userHandler)
    {
        $this->em = $em;
        $this->dayHandler = $dayHandler;
        $this->userHandler = $userHandler;
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

            $startTimestamp = $form['startDate']->getData();
            $endTimestamp = $form['endDate']->getData();
            $foundDays = $this->getDaysBetweenTimestamps($startTimestamp, $endTimestamp);

            if($this->isSetToBeDeleted($form, $order))
                return true;

            if($this->isOrderValid($form, $foundDays, $order))
            {
                $this->updateDays($order, $foundDays);
                $order = $this->setParticipants($participantList, $order);
                $order->setUser($user);
                $this->setDates($order, $startTimestamp, $endTimestamp);

                if(empty($order->getId()))
                    $this->em->persist($order);

                $this->em->flush();
                return true;
            }

            $form->addError(new FormError('Pasirinktos negalimos dienos.'));
        }
        return false;
    }

    /**
     * @param $form FormInterface
     * @param $foundDays array
     * @param $order Order
     * @return bool
     */
    private function isOrderValid($form, $foundDays, $order)
    {
        $participantList = $form['participants']->getData();
        $startDate = $this->dayHandler->timestampToDate($form['startDate']->getData());
        $endDate = $this->dayHandler->timestampToDate($form['endDate']->getData());
        $actualDays = $this->dayHandler->createDatesBetween($startDate, $endDate);

        if(count($actualDays) !== count($foundDays))
            return false;

        $participants = $this->getEntities($participantList);

        /**
         * @var $foundDay Day
         */
        foreach ($foundDays as $foundDay)
        {
            $unitsSold = $this->getUnitsSold($foundDay) - $order->getParticipants()->count() + count($participants);
            if($foundDay->getCapacity() < $unitsSold)
                return false;
        }

        return true;
    }

    /**
     * @param $participantList string
     * @return mixed Participants
     */
    private function getEntities($participantList)
    {
        $participantsIds = explode(',', $participantList);
        $entities = $this->em->getRepository(Participant::class)->findBy(array('id' => $participantsIds));
        return $entities;
    }

    /**
     * @param $form FormInterface
     * @param $order Order
     * @return bool
     */
    private function isSetToBeDeleted($form, $order)
    {
        if(!empty($form->has('delete')))
        {
            if($form->get('delete')->isClicked()) {
                $this->em->remove($order);
                $this->em->flush();
                return true;
            }
        }
        return false;
    }

    /**
     * @param $order Order
     */
    private function setDates($order, $startTimestamp, $endTimestamp)
    {
        $date = new \DateTime();
        $order->setStartDate($date->setTimestamp(floor($startTimestamp / 1000)));
        $date = new \DateTime();
        $order->setEndDate($date->setTimestamp(floor($endTimestamp / 1000)));
    }

    /**
     * @param $participantList string
     * @param $order Order
     * @return Order
     */
    private function setParticipants($participantList, $order)
    {
        $entities = $this->getEntities($participantList);

        $order = $this->removeUncheckedParticipants($entities, $order);
        /**
         * @var $entity Participant
         * @var $order Order
         */
        foreach ($entities as $entity) {
            if(!$order->getParticipants()->contains($entity))
                $order->addParticipant($entity);
        }
        return $order;
    }

    /**
     * @param $currentParticipants array
     * @param $order Order
     * @return Order
     */
    private function removeUncheckedParticipants($currentParticipants, $order)
    {
        $participantsToRemove = $this->getUncheckedParticipants($currentParticipants, $order);

        foreach($participantsToRemove as $participant)
            $order->removeParticipant($participant);

        return $order;
    }

    /**
     * @param $currentParticipants array
     * @param $order Order
     * @return array
     */
    private function getUncheckedParticipants($currentParticipants, $order)
    {
        $exParticipants = $order->getParticipants();
        $participantsToRemove = array();
        foreach ($exParticipants as $exParticipant)
        {
            if(!in_array($exParticipant, $currentParticipants))
                array_push($participantsToRemove, $exParticipant);
        }

        return $participantsToRemove;
    }

    /**
     * @param $order Order
     * @param $foundDays array
     */
    private function updateDays($order, $foundDays)
    {
        $this->removeOrderFromDays($order, $foundDays);

        if ($foundDays != null) {
            /**
             * @var $foundDay Day
             */
            foreach ($foundDays as $foundDay) {
                if(!$foundDay->getOrders()->contains($order))
                    $foundDay->addOrder($order);
            }
        }
    }

    /**
     * @param $order Order
     * @param $foundDays array
     */
    private function removeOrderFromDays($order, $foundDays)
    {
        $startDate = $order->getStartDate();
        $endDate = $order->getEndDate();

        $previousDays = $this->dayHandler->getDaysBetweenDates($startDate, $endDate);
        /**
         * @var $previousDay Day
         */
        foreach($previousDays as $previousDay)
        {
            if(!in_array($previousDay, $foundDays))
                $previousDay->removerOrder($order);
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
            $day = [
                'participantCount' => $this->getUnitsSold($result),
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

    /**
     * @param $id integer
     * @return null|Order
     */
    public function getOrder($id)
    {
        return $this->em->getRepository(Order::class)->findOneBy(array('id' => $id));
    }

    /**
     * @param $day Day
     * @param $participantsIds array
     * @return int
     */
    private function getUnitsSold($day)
    {
        $participantCount = 0;
        $orders = $day->getOrders();

        /**
         * @var $order Order
         */
        foreach($orders as $order)
        {
            $participantsInOrder = $order->getParticipants();
            $participantCount += $participantsInOrder->count();
        }
        return $participantCount;
    }
    
    /**
     * @param $date integer
     * @return array|null
     */
    public function getOrdersInDay($date)
    {
        $result = $this->getDaysBetweenTimestamps($date, $date);

        if(empty($result))
            return null;

        /**
         * @var $result Day
         */
        $result = $result[0];
        $orders = $result->getOrders();

        if(empty($orders))
            return null;

        $ordersData = array();
        /**
         * @var $order Order
         */
        foreach($orders as $order)
        {
            /**
             * @var $user User
             */
            $user = $order->getUser();
            $data = array(
                'userId' => $user->getId(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'id' => $order->getId(),
                'orderedAt' => $order->getOrderedAt()->getTimestamp(),
                'startDate' => $order->getStartDate()->getTimestamp(),
                'endDate' => $order->getEndDate()->getTimestamp(),
                'participantCount' => $order->getParticipants()->count()
            );
            array_push($ordersData, $data);
        }

        return $ordersData;
    }

    /**
     * @param $order Order
     * @param $user User
     * @return array
     */
    public function getParticipants($order, $user)
    {
        $participantList = $user->getParticipants();
        $activeParticipants = $order->getParticipants();

        $participants = $this->userHandler->participantsToArray($participantList);
        $participants = $this->findOverlapingParticipants($participants, $activeParticipants);

        return $participants;
    }

    /**
     * @param $participants
     * @param $activeParticipants
     * @return mixed
     */
    private function findOverlapingParticipants($participants, $activeParticipants)
    {
        for($i = 0; $i < count($participants); $i++)
        {
            /**
             * @var $activeParticipant Participant
             */
            foreach($activeParticipants as $activeParticipant)
            {
                if($participants[$i]['id'] === $activeParticipant->getId())
                {
                    $participants[$i]['checked'] = true;
                    break;
                }
            }
        }

        return $participants;
    }
}