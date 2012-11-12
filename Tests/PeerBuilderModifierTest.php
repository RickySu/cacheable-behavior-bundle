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
    }
    
    public function testSinglePK()
    {
        $this->simpleBuild('singleprimarykey',"Peertest");
        $this->assertTrue(method_exists('\\PeertestSinglepkPeer', 'retrieveByPK'),'retrieveByPK with single PK');
        $Singlepk=new \PeertestSinglepk();
        $Singlepk->setId(1);
        $Singlepk->save();
        $Singlepk=\PeertestSinglepkPeer::retrieveByPK(1);
        $this->assertTrue($Singlepk instanceof \PeertestSinglepk,'retrieveByPK with no cache');
        $Singlepk=\PeertestSinglepkPeer::retrieveByPK(1);        
        $this->assertTrue($Singlepk instanceof MockContainer,'retrieveByPK with cache hit');        
    }
    
    public function testMultiplePK()
    {
        $this->simpleBuild('multipleprimarykey',"Peertest");
        $this->assertTrue(method_exists('\\PeertestMultiplepkPeer', 'retrieveByPK'),'retrieveByPK with multiple PK');
        $Multiplepk=new \PeertestMultiplepk();
        $Multiplepk->setPk1(1);
        $Multiplepk->setPk2(1);
        $Multiplepk->save();
        $Multiplepk=\PeertestMultiplepkPeer::retrieveByPK(1,1);
        $this->assertTrue($Multiplepk instanceof \PeertestMultiplepk,'retrieveByPK with no cache');
        $Multiplepk=\PeertestMultiplepkPeer::retrieveByPK(1,1);        
        $this->assertTrue($Multiplepk instanceof MockContainer,'retrieveByPK with cache hit');        
    }    
    
    public function testSingleUniqueKey()
    {
        $this->simpleBuild('singleuniquekey','Peertest');
        $this->assertTrue(method_exists('\\PeertestSingleuniquekeyPeer', 'retrieveByKey1'),'retrieveByKey1 with single unique key');
        $Singleuniquekey=new \PeertestSingleuniquekey();
        $Singleuniquekey->setId(1);
        $Singleuniquekey->setKey1(1);
        $Singleuniquekey->save();
        $Singleuniquekey=\PeertestSingleuniquekeyPeer::retrieveByKey1(1);
        $this->assertTrue($Singleuniquekey instanceof \PeertestSingleuniquekey,'retrieveByKey1 with no cache');
        $Singleuniquekey=\PeertestSingleuniquekeyPeer::retrieveByKey1(1);        
        $this->assertTrue($Singleuniquekey instanceof MockContainer,'retrieveByKey1 with cache hit');                
    }
    public function testMultipleUniqueKey()
    {
        $this->simpleBuild('multipleuniquekey','Peertest');
        $this->assertTrue(method_exists('\\PeertestMultipleuniquekeyPeer', 'retrieveByKey1Key2'),'retrieveByKey1Key2 with Multiple unique key');
        $Multipleuniquekey=new \PeertestMultipleuniquekey();
        $Multipleuniquekey->setId(1);
        $Multipleuniquekey->setKey1(1);
        $Multipleuniquekey->setKey2(2);
        $Multipleuniquekey->save();
        $Multipleuniquekey=\PeertestMultipleuniquekeyPeer::retrieveByKey1Key2(1,2);
        $this->assertTrue($Multipleuniquekey instanceof \PeertestMultipleuniquekey,'retrieveByKey1Key2 with no cache');
        $Multipleuniquekey=\PeertestMultipleuniquekeyPeer::retrieveByKey1Key2(1,2);        
        $this->assertTrue($Multipleuniquekey instanceof MockContainer,'retrieveByKey1Key2 with cache hit');                
    }
}