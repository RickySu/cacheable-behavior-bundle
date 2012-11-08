<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class PeerBuilderModifierTest extends Base
{
    public function setup()
    {
        $this->prepareMockTagcache();        
        //$this->simpleBuild('multipleprimarykey');
    }
    
    public function testSinglePK()
    {
        $this->simpleBuild('singleprimarykey');
        $this->assertTrue(method_exists('SinglepkPeer', 'retrieveByPK'),'retrieveByPK with single PK');
        $Singlepk=new \Singlepk();
        $Singlepk->setId(1);
        $Singlepk->save();
        $Singlepk=\SinglepkPeer::retrieveByPK(1);
        $this->assertTrue($Singlepk instanceof \Singlepk,'retrieveByPK with no cache');
        $Singlepk=\SinglepkPeer::retrieveByPK(1);        
        $this->assertTrue($Singlepk instanceof MockContainer,'retrieveByPK with cache hit');        
    }
    
    public function testMultiplePK()
    {
        $this->simpleBuild('multipleprimarykey');
        $this->assertTrue(method_exists('MultiplepkPeer', 'retrieveByPK'),'retrieveByPK with multiple PK');
        $Multiplepk=new \Multiplepk();
        $Multiplepk->setPk1(1);
        $Multiplepk->setPk2(1);
        $Multiplepk->save();
        $Multiplepk=\MultiplepkPeer::retrieveByPK(1,1);
        $this->assertTrue($Multiplepk instanceof \Multiplepk,'retrieveByPK with no cache');
        $Multiplepk=\MultiplepkPeer::retrieveByPK(1,1);        
        $this->assertTrue($Multiplepk instanceof MockContainer,'retrieveByPK with cache hit');        
    }    
    
    public function testSingleUniqueKey()
    {
        $this->simpleBuild('singleuniquekey');
        $this->assertTrue(method_exists('SingleuniquekeyPeer', 'retrieveByKey1'),'retrieveByKey1 with single unique key');
        $Singleuniquekey=new \Singleuniquekey();
        $Singleuniquekey->setId(1);
        $Singleuniquekey->setKey1(1);
        $Singleuniquekey->save();
        $Singleuniquekey=\SingleuniquekeyPeer::retrieveByKey1(1);
        $this->assertTrue($Singleuniquekey instanceof \Singleuniquekey,'retrieveByKey1 with no cache');
        $Singleuniquekey=\SingleuniquekeyPeer::retrieveByKey1(1);        
        $this->assertTrue($Singleuniquekey instanceof MockContainer,'retrieveByKey1 with cache hit');                
    }
    public function testMultipleUniqueKey()
    {
        $this->simpleBuild('multipleuniquekey');
        $this->assertTrue(method_exists('MultipleuniquekeyPeer', 'retrieveByKey1Key2'),'retrieveByKey1Key2 with Multiple unique key');
        $Multipleuniquekey=new \Multipleuniquekey();
        $Multipleuniquekey->setId(1);
        $Multipleuniquekey->setKey1(1);
        $Multipleuniquekey->setKey2(2);
        $Multipleuniquekey->save();
        $Multipleuniquekey=\MultipleuniquekeyPeer::retrieveByKey1Key2(1,2);
        $this->assertTrue($Multipleuniquekey instanceof \Multipleuniquekey,'retrieveByKey1Key2 with no cache');
        $Multipleuniquekey=\MultipleuniquekeyPeer::retrieveByKey1Key2(1,2);        
        $this->assertTrue($Multipleuniquekey instanceof MockContainer,'retrieveByKey1Key2 with cache hit');                
    }
    
}