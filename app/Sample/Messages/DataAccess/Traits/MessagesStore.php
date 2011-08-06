<?php

namespace Sample\Messages\DataAccess\Traits;

use SimpleCassie;
use Sample\Users\Domain\User;
use Sample\Messages\Domain\Message;
use Functional as F;
use cassandra_ColumnOrSuperColumn as Column;

trait MessagesStore
{
    private static $INBOX_KEYSPACE = 'Messages_Inbox_Test';
    private static $OUTBOX_KEYSPACE = 'Messages_Outbox_Test';
    
    private static $simpleCassie;
    
    private function simpleCassie()
    {
        if (!self::$simpleCassie) {
            self::$simpleCassie = new SimpleCassie('localhost',  9160, 100);
        }
        
        return self::$simpleCassie;
    }
    
    private function storeIncomingMessage(Message $message, User $recipient)
    {
        // store message in inbox
        $this->simpleCassie()
             ->keyspace(self::$INBOX_KEYSPACE)
             ->cf('messages')
             ->key('user_' . $recipient->getId())
             ->column('message_' . $message->getId())
             ->set((string)$message);
        
        // store message grouped in threads
        $this->simpleCassie()
             ->keyspace(self::$INBOX_KEYSPACE)
             ->cf('threads')
             ->key('user_' . $recipient->getId())
             ->supercolumn('message_' . ($message->isParent() ? $message->getId() : $message->getParentMessageId()))
             ->column('message_' . $message->getId())
             ->set(json_encode($message->toStruct()));
        
        error_log('storing message: ' . json_encode($message->toStruct()));
    }
    
    private function storeOutgoingMessage(Message $message, User $sender)
    {
        throw new \Exception('not yet implemented');
    }
    
    private function findReceivedMessages(User $recipient, $maxResults)
    {
        $res = $this->simpleCassie()
                    ->keyspace(self::$INBOX_KEYSPACE)
                    ->cf('messages')
                    ->key('user_' . $recipient->getId())
                    ->column('message_00000000-0000-0000-0000-000000000000', 'message_zzzzzzzz-zzzz-zzzz-zzzz-zzzzzzzzzzzz')
                    ->slice($maxResults, false);
        
        error_log('### cassandra result: ' . count($res) . ' / max: ' . $maxResults);
        
        $res = F\map($res, function(Column $col) {
            return json_decode($col->column->value, true);
        });
        
        $res = F\map($res, array('Sample\Messages\Domain\Message', 'fromStruct'));
        
        return $res;
    }
    
    private function findThread(User $recipient, $maxResults)
    {
        throw new \Exception('not yet implemented');
/*
        $res = $this->simpleCassie
                    ->keyspace(self::INBOX_KEYSPACE)
                    ->cf('threads')
                    ->key('user_3be7c0e589153c7ae84fdf4c4ac4f360')
                    ->supercolumn('message_' . $id)
                    ->slice(15, true);
*/  
    }
}
