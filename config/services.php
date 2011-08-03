<?php

$app['dataAccess.users.userRepository'] = $app->share(function() use($app) {
    return $app['db.orm.em']->getRepository('Sample\Users\Domain\User');
});

