<?php

namespace Sample\Messages\Domain;

use ArrayObject;
use Countable;
use IteratorAggregate;

class MessageRecipients implements Countable, IteratorAggregate
{
    private $users = array();
    
    public function __construct($users)
    {
        $this->validateMinimumAmount($users);
        $this->validateMaximumAmount($users);
        
        $this->users = $users;
    }
    
    private function validateMinimumAmount(array $users)
    {
        
    }
    
    private function validateMaximumAmount(array $users)
    {
        
    }

    public function count() 
    {
        return count($this->users);
    }

    public function getIterator() 
    {
        return new ArrayObject($this->users);
    }
}