<?php

class EntityDependenciesInjector
{
    private $app;
    
    public function __construct(Silex\Application $app)
    {
        $this->app = $app;
    }
    
    public function postLoad(Doctrine\ORM\Event\LifecycleEventArgs $eventArgs)
    {
        $entity = $eventArgs->getEntity();
        
        if ($entity instanceof Sample\Users\Domain\User) {
            $entity->injectPostbox($this->app['services.users.postbox']($entity));
        }
    }
    
    public function registerEvents()
    {
        $this->app['db.event_manager']->addEventListener(Doctrine\ORM\Events::postLoad, $this);
    }
}
