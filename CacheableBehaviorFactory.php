<?php

namespace RickySu\CacheableBehaviorBundle;

use Symfony\Component\EventDispatcher\Event;
use RickySu\CacheableBehaviorBundle\Event\EventProxy;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent;
use RickySu\Tagcache\TagcacheFactory;

abstract class CacheableBehaviorFactory {

    protected static $Instance = null;

    final public function __construct() {

    }

    public static function init() {
        $Class=get_called_class();
        $dispatcher = EventProxy::getInstance();
        $dispatcher->addListener(EventProxy::GET_TAG_CACHE, array(new $Class(), 'onGetTagcache'));
    }

    final public function onGetTagcache(GetTagcacheEvent $Event) {
        $Event->setTagcache($this->getInstance());
    }

    final protected function getInstance() {
        if (self::$Instance) {
            return self::$Instance;
        }
        self::$Instance = TagcacheFactory::factory($this->getConfig());
        return self::$Instance;
    }

    abstract public function getConfig();

}
