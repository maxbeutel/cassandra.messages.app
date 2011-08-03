<?php

require_once APPLICATION_PATH . '/vendor/SimpleCassie.php';

$app->register(new Silex\Extension\DoctrineExtension(), array(
    'db.options'            => array(
        'driver'    => 'pdo_mysql',
        'dbname'    => 'cassandra_test',
        'user'      => 'root',
        'password'  => 'password',
    ),
    'db.dbal.class_path'    => APPLICATION_PATH . '/vendor/Doctrine',
    'db.common.class_path'  => APPLICATION_PATH . '/vendor/Doctrine',
));

$app->register(new Nutwerk\Extension\DoctrineORMExtension(), array(
    'db.orm.class_path'            => APPLICATION_PATH . '/vendor/Doctrine',
    'db.orm.proxies_dir'           => '/tmp',
    'db.orm.proxies_namespace'     => 'DoctrineProxy',
    'db.orm.auto_generate_proxies' => true,
    'db.orm.entities'              => array(array(
        'type'      => 'annotation', 
        'path'      => APPLICATION_PATH . '/app', 
        'namespace' => 'Sample',
    )),
));

$app->register(new Silex\Extension\TwigExtension(), array(
    'twig.path'       => APPLICATION_PATH . '/views',
    'twig.class_path' => APPLICATION_PATH . '/vendor/Silex/vendor/twig/lib',
));

/*
$app->register(new Simplecassie\Extension\CassandraExtension(), array(
    'simplecassie.class_path'       => APPLICATION_PATH . '/vendor/SimpleCassie.php',
    'simplecassie.host'             => 'localhost',
    'simplecassie.port'             => 9160,
    'simplecassie.timeout'          => 100,
    'simplecassie.testConnection'   => true,
));
*/

$app->register(new Silex\Extension\MonologExtension(), array(
    'monolog.logfile'       => APPLICATION_PATH . '/log/app.log',
    'monolog.class_path'    => APPLICATION_PATH . '/vendor/Silex/vendor/monolog/src',
));