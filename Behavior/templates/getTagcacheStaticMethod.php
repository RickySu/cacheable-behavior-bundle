
/**
 * get TagcacheInstance.
 *
 * @return RickySu\TagcacheBundle\TagCahe\TagcacheAdapter
 */
 static protected function getTagcache()
 {
     $dispatcher=RickySu\CacheableBehaviorBundle\Event\EventProxy::getInstance();
     $Event=$dispatcher->dispatch(RickySu\CacheableBehaviorBundle\Event\EventProxy::GET_TAG_CACHE, new RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent());
     return $Event->getTagcache();
 }
