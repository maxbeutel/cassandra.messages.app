<?php

namespace Sample\Users\Domain;

use Sample\Messages\Domain\Postbox;

/**
 * @Entity(repositoryClass="Sample\Users\DataAccess\UserRepository")
 * @Table(name="user")
 */
class User
{
    use Traits\Profile;
    
    /**
     * @Id 
     * @Column(type="integer", name="user_id")
     * @GeneratedValue
     */    
    private $id;

    /** @Column(type="array", name="user_friend_ids") */
    private $friendIds = array();
    
    public function __construct()
    {
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function postbox()
    {
        return new Postbox($this);
    }
    
    public function isContactOf(User $user)
    {
        return in_array($user->getId(), $this->friendIds);
    }
}
