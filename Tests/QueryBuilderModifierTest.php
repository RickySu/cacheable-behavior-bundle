<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class QueryBuilderModifierTest extends Base
{
    public function setup()
    {
        $this->prepareMockTagcache();
    }
    
    public function testSinglePK()
    {
        $this->simpleBuild('singleprimarykey',"Querytest");
        $this->assertTrue(method_exists('\\QuerytestSinglepkQuery', 'findPK'),'findPk with single PK');
        $Singlepk=new \QuerytestSinglepk();
        $Singlepk->setId(1);
        $Singlepk->save();
        $Singlepk=\QuerytestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk instanceof \QuerytestSinglepk,'findPk with no cache');
        $Singlepk=\QuerytestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk instanceof MockContainer,'findPK with cache hit');        
    }
    
    public function testMultiplePK()
    {
        $this->simpleBuild('multipleprimarykey',"Querytest");
        $this->assertTrue(method_exists('\\QuerytestMultiplepkQuery', 'findPk'),'findPk with multiple PK');
        $Multiplepk=new \QuerytestMultiplepk();
        $Multiplepk->setPk1(1);
        $Multiplepk->setPk2(1);
        $Multiplepk->save();
        $Multiplepk=\QuerytestMultiplepkQuery::create()->findPK(array(1,1));
        $this->assertTrue($Multiplepk instanceof \QuerytestMultiplepk,'findPK with no cache');
        $Multiplepk=\QuerytestMultiplepkQuery::create()->findPK(array(1,1));
        $this->assertTrue($Multiplepk instanceof MockContainer,'findPK with cache hit');        
    }    
    
    public function testSingleUniqueKey()
    {
        $this->simpleBuild('singleuniquekey','Querytest');
        $Singleuniquekey=new \QuerytestSingleuniquekey();
        $Singleuniquekey->setId(1);
        $Singleuniquekey->setKey1(1);
        $Singleuniquekey->save();        
        $Singleuniquekey=\QuerytestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singleuniquekey instanceof \QuerytestSingleuniquekey,'findOneByKey1 with no cache');                
        $Singleuniquekey=\QuerytestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singleuniquekey instanceof MockContainer,'findOneByKey1 with cache hit');
        $Singleuniquekeys=\QuerytestSingleuniquekeyQuery::create()->findByKey1(1);        
        $this->assertTrue(count($Singleuniquekeys)==1,'count findByKey1 with cache hit');
        $this->assertTrue($Singleuniquekeys[0] instanceof MockContainer,'findByKey1 with cache hit');
        $Singleuniquekey=\QuerytestSingleuniquekeyQuery::create()->filterByKey1(1)->findOne();        
        $this->assertTrue($Singleuniquekey instanceof MockContainer,'filterByKey1 with cache hit and findOne');
        $Singleuniquekeys=\QuerytestSingleuniquekeyQuery::create()->filterByKey1(1)->find();        
        $this->assertTrue($Singleuniquekeys[0] instanceof MockContainer,'filterByKey1 with cache hit and find');
    }

    public function testMultipleUniqueKey()
    {
        $this->simpleBuild('multipleuniquekey','Querytest');        
        $Multipleuniquekey=new \QuerytestMultipleuniquekey();
        $Multipleuniquekey->setId(1);
        $Multipleuniquekey->setKey1(1);
        $Multipleuniquekey->setKey2(2);
        $Multipleuniquekey->save();
        $Multipleuniquekey=\QuerytestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multipleuniquekey instanceof \QuerytestMultipleuniquekey,'filterByKey1 filterByKey2 with no cache');
        $Multipleuniquekey=\QuerytestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multipleuniquekey instanceof MockContainer,'filterByKey1 filterByKey2 and findOne with cache hit');
        $Multipleuniquekey=\QuerytestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->filterById(1)->findOne();        
        $this->assertTrue($Multipleuniquekey instanceof \QuerytestMultipleuniquekey,'filterByKey1 filterByKey2 filterById with no cache');
        $Multipleuniquekey=\QuerytestMultipleuniquekeyQuery::create()->filterByKey2(2)->filterByKey1(1)->findOne();
        $this->assertTrue($Multipleuniquekey instanceof MockContainer,'filterByKey2 filterByKey1 and findOne with cache hit');
        $Multipleuniquekeys=\QuerytestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->find();
        $this->assertTrue(count($Multipleuniquekeys)==1,'count filterByKey1 filterByKey2 and find with cache hit');
        $this->assertTrue($Multipleuniquekeys[0] instanceof MockContainer,'filterByKey1 filterByKey2 and find with cache hit');
        $Multipleuniquekeys=\QuerytestMultipleuniquekeyQuery::create()->filterByKey2(2)->filterByKey1(1)->find();
        $this->assertTrue($Multipleuniquekeys[0] instanceof MockContainer,'filterByKey2 filterByKey1 and find with cache hit');
    } 
 
}