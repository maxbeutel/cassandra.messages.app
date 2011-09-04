<?php

namespace Sample\Messages\Domain;

use Sample\Users\Domain\User;
use Sample\Messages\DataAccess\Traits;

class Postbox
{
    use Traits\MessagesStore;
    
    private $owner;
    
    public function __construct(User $owner)
    {
        $this->owner = $owner;
    }
    
    public function addIncomingMessage(Message $message)
    {
        if ($message->getSender() === $this->owner) {
            // message to itself
        }
        
        if (!$message->getSender()->isContactOf($this->owner)) {
            // not allowed to send a message to this guy
        }
        
        $this->storeIncomingMessage($message, $this->owner);
    }
    
    public function addOutgoingMessage(Message $message)
    {
        if ($message->getSender() !== $this->owner) {
            // cant be in owners outbox
        }
        
        $this->storeOutgoingMessage($message, $this->owner);
    }
    
    public function receivedMessages($maxResults)
    {
        return $this->findReceivedMessages($this->owner, $maxResults);
    }
    
    public function threadFor($messageInThreadId)
    {
        $thread = $this->findThread($this->owner, $this->findReceivedMessage($this->owner, $messageInThreadId));
    }
}