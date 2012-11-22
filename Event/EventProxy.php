<?php

namespace RickySu\CacheableBehaviorBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;

class EventProxy
{
    const GET_TAG_CACHE = 'cacheable_behavior.get_tagcache';
    protected static $Instance = null;

    public static function factory()
    {
        return new EventDispatcher();
    }

    /**
     *
     * @return EventDispatcher
     */
    public static function getInstance()
    {
        if (self::$Instance !== null) {
            return self::$Instance;
        }
        self::$Instance = self::factory();

        return self::$Instance;
    }

}
