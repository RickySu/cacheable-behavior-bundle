
/**
 * get TagcacheInstance.
 *
 * @return \RickySu\Tagcache\TagCahe\TagcacheAdapter
 */
 <?php if (isset($static) && $static):?>static <?php endif?>protected function getTagcache() {
     $dispatcher=\RickySu\CacheableBehaviorBundle\Event\EventProxy::getInstance();
     $Event=$dispatcher->dispatch(\RickySu\CacheableBehaviorBundle\Event\EventProxy::GET_TAG_CACHE, new \RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent());

     return $Event->getTagcache();
 }
