
/**
 * get TagcacheInstance.
 *
 * @return RickySu\TagcacheBundle\TagCahe\TagcacheAdapter
 */
 static protected function getTagcache()
 {
     $dispatcher=EventProxy::getInstance();
     $Event=$dispatcher->dispatch(EventProxy::GET_TAG_CACHE, new GetTagcacheEvent());
     return $Event->getTagcache();
 }
