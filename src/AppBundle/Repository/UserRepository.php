<?php

namespace AppBundle\Repository;

class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByApiId($id)
    {
        $query = $this->getEntityManager()->find('AppBundle\Entity\User',':apiId')->setParameter('apiId', $id)->getQuery();
        return $query->getOneOrNullResult();
    }
}
