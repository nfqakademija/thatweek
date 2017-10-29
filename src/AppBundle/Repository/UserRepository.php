<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;


class UserRepository extends EntityRepository
{
    public function findByApiId($id)
    {
        $query = $this->getEntityManager()->find('AppBundle\Entity\User',':apiId')->setParameter('apiId', $id)->getQuery();
        return $query->getOneOrNullResult();
    }
}
