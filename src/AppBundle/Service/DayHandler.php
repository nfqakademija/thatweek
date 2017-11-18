<?php

namespace AppBundle\Service;

use AppBundle\Entity\Day;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class DayHandler
{

    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function handle(Request $request,FormInterface $form)
    {
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $startDate = $this->timestampToDate($form['startDate']->getData());
            $endDate = $this->timestampToDate($form['endDate']->getData());
            $capacity = $form['capacity']->getData();

            $this->updateDays($startDate, $endDate, $capacity);

            $this->em->flush();
        }
    }

    /**
     * @param $startDates \DateTime
     * @param $endDates \DateTime
     * @return mixed
     */
    public function getDaysBetweenDates($startDate, $endDate)
    {
        return $this->em->getRepository(Day::class)->findBetween($startDate, $endDate);
    }

    /**
     * @param $timestamp
     * @return \DateTime
     */
    public function timestampToDate($timestamp)
    {
        $date = new \DateTime();
        $date->setTimestamp($timestamp/1000);
        return $date;
    }

    /**
     * @param $startDate \DateTime
     * @param $endDate \DateTime
     * @param $capacity integer
     */
    private function updateDays($startDate, $endDate, $capacity)
    {
        $actualDays = $this->createDatesBetween($startDate, $endDate);
        $foundDays = $this->getDaysBetweenDates($startDate, $endDate);
        $this->createNotExistingDays($foundDays, $actualDays, $capacity);

        if($foundDays != null) {
            /**
             * @var $foundDay Day
             */
            foreach ($foundDays as $foundDay) {
                $foundDay->setCapacity($capacity);
            }
        }
    }

    /**
     * @param $startDate \DateTime
     * @param $endDate \DateTime
     * @return array
     */
    public function createDatesBetween($startDate, $endDate)
    {
        $days = array();

        $start = clone $startDate;
        while($start <= $endDate)
        {
            $day = clone $start;
            array_push($days, $day);
            $start->add(new \DateInterval('P1D'));
        }
        return $days;
    }

    /**
     * @param $foundDays array
     * @param $days array
     */
    private function createNotExistingDays($foundDays, $days, $capacity)
    {
        /**
         * @var $day \DateTime
         */
        foreach($days as $day)
        {
            $contains = false;

            if($foundDays != null) {
                /**
                 * @var $foundDay Day
                 */
                foreach ($foundDays as $foundDay) {
                    if ($day == $foundDay->getDate()) {
                        $contains = true;
                        break;
                    }
                }
            }
            if($contains == false)
            {
                $newDay = new Day();
                $newDay->setDate($day);
                $newDay->setCapacity($capacity);
                $this->em->persist($newDay);
            }
        }
    }
}