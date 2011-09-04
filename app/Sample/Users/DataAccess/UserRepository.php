<?php

namespace Sample\Users\DataAccess;

use Doctrine\ORM\EntityRepository;
use InvalidArgumentException;

class UserRepository extends EntityRepository
{
    public function findById($id)
    {
        return $this->_em
                    ->createQuery('SELECT u FROM Sample\Users\Domain\User u where u.id = :id')
                    ->setParameter('id', $id)
                    ->getSingleResult();
    }
    
    public function findByIds($ids)
    {
        if (count($ids) === 0) {
            throw new InvalidArgumentException('ids cant be empty');
        }
        
        return $this->_em
                    ->createQuery('SELECT u FROM Sample\Users\Domain\User u where ' . $this->_em->createQueryBuilder('u')->expr()->in('u.id', $ids))
                    ->getResult();
    }
}