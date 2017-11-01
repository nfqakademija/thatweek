<?php

namespace AppBundle\Utils;

use Doctrine\ORM\EntityManager;
use AppBundle\Entity\Week;

class Calendar
{

    private $monthsToLoad = 6;
    private $em;
    private $fromDate;
    private $toDate;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->fromDate = $this->getFromDate();
        $this->toDate = $this->getToDate();
    }

    public function getWeeks()
    {
        $weeks = $this->em->getRepository(Week::class)->findBetween($this->fromDate, $this->toDate);
        return $this->createWeeks($weeks);
    }

    private function createWeeks($weeks)
    {
        $weeksFound = count($weeks);

        if($weeksFound > 0) {
            $dateOfLastMonday = $weeks[count($weeks) - 1]['startDate'];
            $dateOfLastMonday = $this->nextWeek($dateOfLastMonday);
        }
        else
            $dateOfLastMonday = $this->getFirstMonday();

       while($dateOfLastMonday < $this->toDate)
        {
            $week = $this->getNewWeek($dateOfLastMonday);
            array_push($weeks, $this->weekToArray($week));
            $this->em->persist($week);
            $dateOfLastMonday = $this->nextWeek($dateOfLastMonday);

        }
        $this->em->flush();

        return $weeks;
    }

    private function getFromDate()
    {
        return strtotime(date('Y-m-01'));
    }

    private function getToDate()
    {
        return strtotime('+'.$this->monthsToLoad.' months', $this->fromDate);
    }

    private function getFirstMonday()
    {
        return strtotime('first monday of this month');
    }

    private function nextWeek($date)
    {
        return strtotime('+1 week', $date);
    }

    private function getNewWeek($startDate)
    {
        $week = new Week();
        $week->setStartDate($startDate);
        $endOfWeekDate = strtotime('+6 days', $startDate);
        $week->setEndDate($endOfWeekDate);
        $week->setUnitsSold(0);
        return $week;
    }

    private function weekToArray($week)
    {
        return array('startDate' => $week->getStartDate(),
            'EndDate' => $week->getEndDate(),
            'unitsSold' => $week->getUnitsSold()
        );
    }
}