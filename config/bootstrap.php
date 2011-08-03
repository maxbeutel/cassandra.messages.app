<?php

define('APPLICATION_PATH', realpath(__DIR__ . '/..'));

require_once '../vendor/Silex/autoload.php';

$app = new Silex\Application();

require_once 'autoload.php';
require_once 'extensions.php';
require_once 'services.php';
