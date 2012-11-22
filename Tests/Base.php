<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Event\EventProxy;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockTagcache;

class Base extends \PHPUnit_Framework_TestCase
{
    protected $Tagcache=null;

    protected function LoadSchema($SchemaFile,$Namespace)
    {
        $Schema=file_get_contents(__DIR__ . "/schema/$SchemaFile.xml");

        return str_replace("#Namespace#", $Namespace, $Schema);
    }

    protected function simpleBuild($SchemaFile,$Namespace)
    {
        $XML = $this->LoadSchema($SchemaFile,$Namespace);
        $builder = new \PropelQuickBuilder();
        $config = $builder->getConfig();
        $config->setBuildProperty('behaviorCacheableClass', 'RickySu\\CacheableBehaviorBundle\\Behavior\\CacheableBehavior');
        $builder->setConfig($config);
        $builder->setSchema($XML);
        $builder->build();
    }

    public function prepareMockTagcache()
    {
        $dispatcher = EventProxy::getInstance();
        $dispatcher->addListener(EventProxy::GET_TAG_CACHE, array($this, 'onGetTagcache'));
    }

    public function onGetTagcache(GetTagcacheEvent $Event)
    {
        if (!$this->Tagcache) {
           $this->Tagcache=new MockTagcache(null,null);
        }
        $Event->setTagcache($this->Tagcache);
    }

}
