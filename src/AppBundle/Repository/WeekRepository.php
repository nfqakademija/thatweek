<?php

namespace AppBundle\Repository;

class WeekRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBetween($fromDate, $toDate)
    {
        return $this->createQueryBuilder('week')
            ->where('week.startDate >= :fromDate')
            ->andWhere('week.startDate >= :fromDate')
            ->andWhere('week.startDate <= :toDate')
            ->andWhere('week.startDate <= :toDate')
            ->setParameters(array('fromDate' => $fromDate, 'toDate' => $toDate))
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

    }
}
