<?php

namespace Sample\Messages\Domain;

use Sample\Users\Domain\User;
use SimpleCassieUuid;
use Functional as F;
use BadMethodCallException;

class Message
{
    private $id;
    
    private $sender;
    private $senderId;
    
    private $subject;
    
    private $body;
    
    private $recipients;
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
    
    // internal to message package
    public static function fromStruct(array $struct)
    {
        $message = new static($struct['subject'], $struct['body']);
        
        $message->id = $struct['id'];
        $message->senderId = $struct['senderId'];
        $message->recipientIds = $struct['recipientIds'];
        
        return $message;
    }
    
    // internal to message package
    public function toStruct()
    {
        return array(
            'id'            => $this->id,
            'subject'       => $this->subject,
            'body'          => $this->body,
            'senderId'      => $this->senderId,
            'recipientIds'  => $this->recipientIds,
        );
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
    
    public function getSender()
    {
        return $this->sender;
    }
    
    // internal to messages namespace
    public function getSenderId()
    {
        return $this->senderId;
    }
    
    // internal to messages namespace
    public function setSender(User $sender)
    {
        if ($this->sender) {
            throw new BadMethodCallException('Sender already set');
        }
        
        $this->sender = $sender;
    }
    

    public function getRecipients()
    {
        return $this->recipients;
    }
    
    // internal to messages namespace
    public function getRecipientIds()
    {
        return $this->recipientIds;
    }
    
    // internal to messages namespace
    public function setRecipients(array $recipients)
    {
        if (count($this->recipients)) {
            throw new BadMethodCallException('Recipients already set');
        }
        
        $this->recipients = $recipients;
    }
    
    public function isParent()
    {
        // always returns true for now until chil messages are implemented
        return true;
    }
    
    public function getParentMessageId()
    {
        throw new \Exception('not yet implemented');
    }
    
    public function sendTo(MessageRecipients $recipients, User $sender)
    {
        $this->sender = $sender;
        $this->senderId = $sender->getId();
        
        $this->recipients = $recipients;
        $this->recipientIds = F\Invoke($recipients, 'getId');
        
        foreach ($recipients as $recipient) {
            $recipient->postbox()->addIncomingMessage($this);
        }
    }
    
    public function replyTo(Message $parentMessage)
    {
        throw new \Exception('not yet implemented');
    }
}