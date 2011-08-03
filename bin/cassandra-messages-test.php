<?php

@unlink('data');

print 'use Messages_test;' . chr(10);


for ($i = 2; $i < 1000; $i++) {
    for ($j = 2; $j < 500; $j++) {
        print 'set messages[user_' . $i . '][message_' . $j . '][subject] = \'some subject\';' . chr(10);
        print 'set messages[user_' . $i . '][message_' . $j . '][body] = \'' . str_repeat('x', 200) . '\';' . chr(10);
    }
}

