<?php

namespace RickySu\CacheableBehaviorBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

class EventProxy {
    const GET_TAG_CACHE = 'cacheable_behavior.get_tagcache';
    static protected $Instance = null;

    static public function factory() {
        return new EventDispatcher();
    }

    /**
     *
     * @return EventDispatcher
     */
    static public function getInstance() {
        if (self::$Instance !== null) {
            return self::$Instance;
        }
        self::$Instance = self::factory();
        return self::$Instance;
    }

}