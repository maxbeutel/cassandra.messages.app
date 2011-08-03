<?php

$app['autoloader']->registerNamespace('Sample', APPLICATION_PATH . '/app');
$app['autoloader']->registerNamespace('Doctrine', APPLICATION_PATH . '/vendor');
$app['autoloader']->registerNamespace('Nutwerk', APPLICATION_PATH . '/vendor/Silex-extensions/nutwerk-orm-extension/lib');
$app['autoloader']->registerNamespace('Simplecassie', APPLICATION_PATH . '/vendor/Silex-extensions/simplecassie-cassandra-extension/lib');

