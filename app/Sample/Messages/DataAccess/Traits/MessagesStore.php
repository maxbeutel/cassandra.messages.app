<?php

namespace Sample\Messages\DataAccess\Traits;

use SimpleCassie;
use Sample\Users\Domain\User;
use Sample\Users\DataAccess\UserRepository;
use Sample\Messages\Domain\Message;
use Functional as F;

trait MessagesStore
{
    private static $INBOX_KEYSPACE = 'Messages_Inbox_Test';
    private static $OUTBOX_KEYSPACE = 'Messages_Outbox_Test';
    
    private $simpleCassie;
    
    private $userRepository;
    
    public function injectSimpleCassie(SimpleCassie $simpleCassie)
    {
        $this->simpleCassie = $simpleCassie;
    }
    
    public function injectUserRepository(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    
    private function storeIncomingMessage(Message $message, User $recipient)
    {
        // store message in inbox
        $this->simpleCassie
             ->keyspace(self::$INBOX_KEYSPACE)
             ->cf('messages')
             ->key('user_' . $recipient->getId())
             ->column('message_' . $message->getId())
             ->set((string)$message);
        
        // store message grouped in threads
        $this->simpleCassie
             ->keyspace(self::$INBOX_KEYSPACE)
             ->cf('threads')
             ->key('user_' . $recipient->getId())
             ->supercolumn('message_' . ($message->isParent() ? $message->getId() : $message->getParentMessageId()))
             ->column('message_' . $message->getId())
             ->set(json_encode($message));
        
        #error_log('storing message: ' . json_encode($message));
    }
    
    private function storeOutgoingMessage(Message $message, User $sender)
    {
        throw new \Exception('not yet implemented');
    }
    
    private function findReceivedMessages(User $recipient, $maxResults)
    {
        $messages = $this->simpleCassie
                         ->keyspace(self::$INBOX_KEYSPACE)
                         ->cf('messages')
                         ->key('user_' . $recipient->getId())
                         ->column('message_00000000-0000-0000-0000-000000000000', 'message_zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz')
                         ->slice($maxResults, false);
        
        #error_log(print_r($messages, 1));
        #error_log('### cassandra result: ' . count($messages) . ' / max: ' . $maxResults);
        
        return $this->mapUsersToMessages($this->hydrateMessages($messages));
    }
    
    private function findReceivedMessage(User $recipient, $messageId)
    {
        $message = $this->simpleCassie
                        ->keyspace(self::$INBOX_KEYSPACE)
                        ->cf('messages')
                        ->key('user_' . $recipient->getId())
                        ->column('message_' . $messageId)
                        ->get();
        
        #error_log('### finding single received message');
        #error_log('### user id: ' . $recipient->getId());
        #error_log('### msg id: ' . $messageId);
        #error_log('### found: ' . print_r($message, 1));
        
        return $this->mapUsersToMessages($this->hydrateMessages(array($message)))[0];
    }
        
    private function findThread(User $recipient, Message $messageInThread)
    {
        $parentMessageId = $messageInThread->isParent() ? $messageInThread->getId() : $messageInThread->getParentMessageId();
        
        $messages = $this->simpleCassie
                         ->keyspace(self::$INBOX_KEYSPACE)
                         ->cf('threads')
                         ->key('user_' . $recipient->getId())
                         ->supercolumn('message_' . $parentMessageId)
                         ->get();
        
        #error_log('### finding thread');
        #error_log('### user id: ' . $recipient->getId());
        #error_log('### parent msg id: ' . $parentMessageId);
        #error_log('### found: ' . print_r($messages, 1));
        
        return $this->mapUsersToMessages($this->hydrateMessages(!is_array($messages) ? array($messages) : $messages));
    }
    
    
    // utility functions
    
    // map cassandra result objects to message objects
    private function hydrateMessages(array $messages)
    {
        $messages = F\map($messages, function($col) {
            return json_decode($col->column ? $col->column->value : $col->super_column->columns[0]->value, true);
        });
        
        $messages = F\map($messages, array('Sample\Messages\Domain\Message', 'fromStruct'));
        
        return $messages;
    }
     
    // a bit ugly
    // extract all user ids from loaded messages
    // batch load users
    // assign loaded users back to messages
    private function mapUsersToMessages(array $messages)
    {
        $userIds = array();
        
        F\each($messages, function(Message $message) use(&$userIds) {
            $userIds[] = $message->getSenderId();
            $userIds = array_merge($userIds, $message->getRecipientIds());
        });
        
        $users = $this->userRepository->findByIds($userIds);
        
        F\each($messages, function(Message $message) use($users) {
            $senderId = $message->getSenderId();
            $sender = F\first($users, function(User $user) use($senderId) {
                return $user->getId() === $senderId;
            });
            
            $message->setSender($sender);
            
            
            $recipientIds = $message->getRecipientIds();
            $recipients = F\select($users, function(User $user) use($recipientIds) {
                return in_array($user->getId(), $recipientIds, true);
            });
            
            $message->getRecipients($recipients);
        });
        
        return $messages;
    }
}
