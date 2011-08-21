<?php

require_once '../config/bootstrap.php';

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
    
    error_log(print_r($messages, 1));
    
    /*foreach ($messages as $message) {
        error_log('msg id: ' . $message->getId());
        error_log('sender email: ' . $message->getSender()->getEmail());
    }*/
    
    return 'Hello foo';
});

$app->get('/messages/inbox/{id}', function($id) use($app) {
    $user = $app['dataAccess.users.userRepository']->findById(20);
    
    $thread = $user->postbox()->threadFor($id);
    

    return 'Hello foo';
});

$app->run();