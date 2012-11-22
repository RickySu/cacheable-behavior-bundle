<?php
namespace RickySu\CacheableBehaviorBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use RickySu\Tagcache\Adapter\TagcacheAdapter;

class GetTagcacheEvent extends Event
{
    protected $Tagcace=null;

    public function setTagcache(TagcacheAdapter $Tagcache)
    {
        $this->Tagcace=$Tagcache;
    }

    /**
     *
     * @return TagcacheAdapter
     */
    public function getTagcache()
    {
        return $this->Tagcace;
    }
}
