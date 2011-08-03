<?php

namespace Sample\Users\DataAccess;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findById($id)
    {
        return $this->_em
                    ->createQuery('SELECT u FROM Sample\Users\Domain\User u where u.id = :id')
                    ->setParameter('id', $id)
                    ->getSingleResult();
    }
}