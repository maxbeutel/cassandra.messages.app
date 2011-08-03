<?php

namespace Sample\Messages\Domain;

use Sample\Users\Domain\User;
use Sample\Common\Interfaces\DenormalizedEntityData;
use SimpleCassieUuid;
use Functional as F;

class Message implements DenormalizedEntityData
{
    private $id;
    
    private $senderId;
    
    private $subject;
    
    private $body;
    
    private $recipientIds = array();
    
    public function __construct($subject, $body)
    {
        $this->id = (string)new SimpleCassieUuid();
        
        $this->validateSubjectMinLength($subject);
        $this->validateSubjectMaxLength($subject);
        $this->subject = $subject;
        
        $this->validateBodyMinLength($body);
        $this->validateBodyMaxLength($body);
        $this->body = $body;
    }
    
    public static function fromStruct(array $struct)
    {
        $message = new static($struct['subject'], $struct['body']);
        
        $message->id = $struct['id'];
        $message->senderId = $struct['senderId'];
        $message->recipientIds = $struct['recipientIds'];
        
        return $message;
    }
    
    private function validateSubjectMinLength($subject)
    {
        
    }
    
    private function validateSubjectMaxLength($subject)
    {
        
    }
    
    private function validateBodyMinLength($body)
    {
        
    }
    
    private function validateBodyMaxLength($body)
    {
        
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getSenderId()
    {
        return $this->senderId;
    }
    
    public function isParent()
    {
        return true;
    }
    
    public function getParentMessageId()
    {
        throw new \Exception('not yet implemented');
    }
    
    public function sendTo(MessageRecipients $recipients, User $sender)
    {
        $this->senderId = $sender->getId();
        $this->recipientIds = F\Invoke($recipients, 'getId');
        
        foreach ($recipients as $recipient) {
            $recipient->postbox()->addIncomingMessage($this);
        }
    }
    
    public function replyTo(Message $parentMessage)
    {
        throw new \Exception('not yet implemented');
    }
    
    // internal to message package
    public function __toString()
    {
        return json_encode(array(
            'id'            => $this->id,
            'subject'       => $this->subject,
            'body'          => $this->body,
            'senderId'      => $this->senderId,
            'recipientIds'  => $this->recipientIds,
        ));
    }

    public function getEntityIds() 
    {
        return array_merge(array($this->senderId), $this->recipientIds);
    }
}