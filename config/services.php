<?php

// repositories
$app['dataAccess.users.userRepository'] = $app->share(function() use($app) {
    return $app['db.orm.em']->getRepository('Sample\Users\Domain\User');
});

// user specific
$app['services.users.postbox'] = $app->protect(function(Sample\Users\Domain\User $owner) use($app) {
    $postbox = new Sample\Messages\Domain\Postbox($owner, $app['dataAccess.users.userRepository']);
    $postbox->injectSimpleCassie($app['simplecassie']);
    $postbox->injectUserRepository($app['dataAccess.users.userRepository']);
    return $postbox;
});

// util class for injecting dependencies in models
require_once 'util/EntityDependenciesInjector.php';

$injector = new EntityDependenciesInjector($app);
$injector->registerEvents();

