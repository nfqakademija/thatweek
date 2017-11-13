<?php

namespace AppBundle\Repository;

class DayRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param $start \DateTime
     * @param $end \DateTime
     */
    public function findBetween($start, $end)
    {
        return $this->createQueryBuilder('day')
            ->where('day.date >= :startDate AND day.date <= :endDate')
            ->setParameters(array('startDate' => $start, 'endDate' => $end))
            ->orderBy('day.date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
