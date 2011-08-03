<?php

require_once '../config/bootstrap.php';

use Functional as F;

class Frob implements Countable, IteratorAggregate
{
    private $entityGetter;
    private $collection;
    
    private $entities;
    
    public function __construct(array $collection, Closure $entityGetter)
    {
        $this->validateItemsType($collection);
        $this->collection = $collection;
       
        $this->entityGetter = $entityGetter;
    }
    
    private function validateItemsType(array $collection)
    {
        F\each($collection, function($item) {
            assert($item instanceof \Sample\Common\Interfaces\DenormalizedEntityData);
        });
    }
    
    private function loadDenormalizedEntities()
    {
        $entityIds = array_unique(F\flatten(F\invoke($this->collection, 'getEntityIds')));
        return $this->entityGetter->__invoke($entityIds);
    }
    
    public function getEntity($id)
    {
        return F\first($this->entities, function($entity) use($id) {
            return $entity->getId() === $id;
        });
    }
    
    public function count()
    {
        return count($this->collection);
    }
    
    public function getIterator() 
    {
        $this->entities = $this->loadDenormalizedEntities();
        return new ArrayObject($this->collection);
    }
}

$app->get('/', function() use($app) {
    $sender = $app['dataAccess.users.userRepository']->findById(10);
        
    $recipient_1 = $app['repositories.users.userRepository']->findById(20);
    $recipient_2 = $app['repositories.users.userRepository']->findById(30);
    $recipient_3 = $app['repositories.users.userRepository']->findById(40);

    $messageRecipients = new Sample\Messages\Domain\MessageRecipients(array($recipient_1, $recipient_2, $recipient_3));
    
    $message = new Sample\Messages\Domain\Message('message subject', 'message body');
    $message->sendTo($messageRecipients, $sender);
    
    return 'index';
});

$app->get('/messages/inbox', function() use($app) {
    $user = $app['dataAccess.users.userRepository']->findById(40);

    $messages = $user->postbox()->receivedMessages(15);
    $messages = new Frob($messages, function(array $userIds) use($app) {
        return $app['dataAccess.users.userRepository']->findByIds($userIds);
    });
    
    foreach ($messages as $message) {
        error_log('msg id: ' . $message->getId());
        error_log('sender email: ' . $messages->getEntity($message->getSenderId())->getEmail());
        
    }
    
   # error_log(print_r($messages, 1));
    
    return 'Hello foo';
});

$app->get('/messages/inbox/{id}', function($id) use($app) {
    $user = $app['dataAccess.users.userRepository']->findById(10);



    return 'Hello foo';
});

$app->run();