<?php

namespace Sample\Users\Domain\Traits;

trait Profile
{
    /** @Column(type="string", length=50, name="user_email") */
    public $email;
    
    /** @Column(type="string", length=50, name="user_password") */
    public $password;
    
    /** @Column(type="string", length=50, name="user_nickname") */
    public $nickname;
    
    /** @Column(type="string", length=50, name="user_location") */
    public $location;
    
    /** @Column(type="string", length=50, name="user_name") */
    public $name;
    
    public function getEmail()
    {   
        return $this->email;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getName()
    {
        return $this->name;
    }    
}