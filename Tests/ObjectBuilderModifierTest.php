<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierTest extends Base
{
    public function setup()
    {
        $this->prepareMockTagcache();
    }
    
    public function testCacheClearSinglePrimaryKey()
    {
        $this->simpleBuild('singleprimarykey',"Objecttest");
        $Singlepk=new \ObjecttestSinglepk();
        $Singlepk->setId(1);
        $Singlepk->setValue(1);
        $Singlepk->save();
        $Singlepk=\ObjecttestSinglepkQuery::create()->findPk(1);        
        $Singlepk=\ObjecttestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk instanceof MockContainer,'singlepk findPK with cache hit');
        $Singlepk->setValue(2);
        $Singlepk->save();
        $Singlepk=\ObjecttestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk instanceof \ObjecttestSinglepk,'singlepk findPK clear cache after save');
        $Singlepk->delete();
        $Singlepk=\ObjecttestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk == null,'singlepk findPK clear cache after delete');        
    }
    
    public function testCacheClearMultiplePrimaryKey()
    {
        $this->simpleBuild('multipleprimarykey',"Objecttest");
        $Multiplepk=new \ObjecttestMultiplepk();
        $Multiplepk->setPk1(1);
        $Multiplepk->setPk2(2);
        $Multiplepk->setValue(1);
        $Multiplepk->save();
        $Multiplepk=\ObjecttestMultiplepkQuery::create()->findPk(array(1,2));
        $Multiplepk=\ObjecttestMultiplepkQuery::create()->findPk(array(1,2));
        $this->assertTrue($Multiplepk instanceof MockContainer,'multiplepk findPK with cache hit');
        $Multiplepk->setValue(2);
        $Multiplepk->save();
        $Multiplepk=\ObjecttestMultiplepkQuery::create()->findPk(array(1,2));
        $this->assertTrue($Multiplepk instanceof \ObjecttestMultiplepk,'multiplepk findPK clear cache after save');
        $Multiplepk->delete();
        $Multiplepk=\ObjecttestMultiplepkQuery::create()->findPk(array(1,2));
        $this->assertTrue($Multiplepk == null,'multiplepk findPK clear cache after delete');        
    }
    
    public function testCacheClearSingleUniqueKey()
    {
        $this->simpleBuild('singleuniquekey',"Objecttest");
        $Singlepk=new \ObjecttestSingleuniquekey();
        $Singlepk->setId(1);
        $Singlepk->setKey1(1);
        $Singlepk->setValue(1);
        $Singlepk->save();
        $Singlepk=\ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);        
        $Singlepk=\ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singlepk instanceof MockContainer,'singleuniquekey findPK with cache hit');
        $Singlepk->setValue(2);
        $Singlepk->save();
        $Singlepk=\ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singlepk instanceof \ObjecttestSingleuniquekey,'singleuniquekey findPK clear cache after save');
        $Singlepk->delete();
        $Singlepk=\ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singlepk == null,'singleuniquekey findPK clear cache after delete');        
    }
    
    public function testCacheClearMultipleUniqueKey()
    {
        $this->simpleBuild('multipleuniquekey',"Objecttest");
        $Multiplepk=new \ObjecttestMultipleuniquekey();
        $Multiplepk->setId(1);
        $Multiplepk->setKey1(1);
        $Multiplepk->setKey2(2);
        $Multiplepk->setValue(1);
        $Multiplepk->save();
        $Multiplepk=\ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $Multiplepk=\ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multiplepk instanceof MockContainer,'multipleuniquekey findPK with cache hit');
        $Multiplepk->setValue(2);
        $Multiplepk->save();
        $Multiplepk=\ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multiplepk instanceof \ObjecttestMultipleuniquekey,'multipleuniquekey findPK clear cache after save');
        $Multiplepk->delete();
        $Multiplepk=\ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multiplepk == null,'multipleuniquekey findPK clear cache after delete');        
    }    
}