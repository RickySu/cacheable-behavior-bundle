<?php

namespace RickySu\CacheableBehaviorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\EventDispatcher\Event;
use RickySu\CacheableBehaviorBundle\Event\EventProxy;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent;

class CacheableBehaviorBundle extends Bundle {

    public function boot() {        
        $dispatcher = EventProxy::getInstance();
        $dispatcher->addListener(EventProxy::GET_TAG_CACHE, array($this,'onGetTagcache'));
    }

    public function onGetTagcache(GetTagcacheEvent $Event) {
        $Event->setTagcache($this->container->get('tagcache'));
    }

}
