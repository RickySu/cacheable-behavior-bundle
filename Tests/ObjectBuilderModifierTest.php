<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierTest extends Base {

    public function setup() {
        $this->prepareMockTagcache();
    }
/*
    public function testCacheClearSinglePrimaryKey() {
        $this->simpleBuild('singleprimarykey', "Objecttest");
        $Singlepk = new \ObjecttestSinglepk();
        $Singlepk->setId(1);
        $Singlepk->setValue(1);
        $Singlepk->save();
        $Singlepk = \ObjecttestSinglepkQuery::create()->findPk(1);
        $Singlepk = \ObjecttestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk instanceof MockContainer, 'singlepk findPK with cache hit');
        $Singlepk->setValue(2);
        $Singlepk->save();
        $Singlepk = \ObjecttestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk instanceof \ObjecttestSinglepk, 'singlepk findPK clear cache after save');
        $Singlepk->delete();
        $Singlepk = \ObjecttestSinglepkQuery::create()->findPk(1);
        $this->assertTrue($Singlepk == null, 'singlepk findPK clear cache after delete');
    }

    public function testCacheClearMultiplePrimaryKey() {
        $this->simpleBuild('multipleprimarykey', "Objecttest");
        $Multiplepk = new \ObjecttestMultiplepk();
        $Multiplepk->setPk1(1);
        $Multiplepk->setPk2(2);
        $Multiplepk->setValue(1);
        $Multiplepk->save();
        $Multiplepk = \ObjecttestMultiplepkQuery::create()->findPk(array(1, 2));
        $Multiplepk = \ObjecttestMultiplepkQuery::create()->findPk(array(1, 2));
        $this->assertTrue($Multiplepk instanceof MockContainer, 'multiplepk findPK with cache hit');
        $Multiplepk->setValue(2);
        $Multiplepk->save();
        $Multiplepk = \ObjecttestMultiplepkQuery::create()->findPk(array(1, 2));
        $this->assertTrue($Multiplepk instanceof \ObjecttestMultiplepk, 'multiplepk findPK clear cache after save');
        $Multiplepk->delete();
        $Multiplepk = \ObjecttestMultiplepkQuery::create()->findPk(array(1, 2));
        $this->assertTrue($Multiplepk == null, 'multiplepk findPK clear cache after delete');
    }

    public function testCacheClearSingleUniqueKey() {
        $this->simpleBuild('singleuniquekey', "Objecttest");
        $Singlepk = new \ObjecttestSingleuniquekey();
        $Singlepk->setId(1);
        $Singlepk->setKey1(1);
        $Singlepk->setValue(1);
        $Singlepk->save();
        $Singlepk = \ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $Singlepk = \ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singlepk instanceof MockContainer, 'singleuniquekey findPK with cache hit');
        $Singlepk->setValue(2);
        $Singlepk->save();
        $Singlepk = \ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singlepk instanceof \ObjecttestSingleuniquekey, 'singleuniquekey findPK clear cache after save');
        $Singlepk->delete();
        $Singlepk = \ObjecttestSingleuniquekeyQuery::create()->findOneByKey1(1);
        $this->assertTrue($Singlepk == null, 'singleuniquekey findPK clear cache after delete');
    }

    public function testCacheClearMultipleUniqueKey() {
        $this->simpleBuild('multipleuniquekey', "Objecttest");
        $Multiplepk = new \ObjecttestMultipleuniquekey();
        $Multiplepk->setId(1);
        $Multiplepk->setKey1(1);
        $Multiplepk->setKey2(2);
        $Multiplepk->setValue(1);
        $Multiplepk->save();
        $Multiplepk = \ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $Multiplepk = \ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multiplepk instanceof MockContainer, 'multipleuniquekey findPK with cache hit');
        $Multiplepk->setValue(2);
        $Multiplepk->save();
        $Multiplepk = \ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multiplepk instanceof \ObjecttestMultipleuniquekey, 'multipleuniquekey findPK clear cache after save');
        $Multiplepk->delete();
        $Multiplepk = \ObjecttestMultipleuniquekeyQuery::create()->filterByKey1(1)->filterByKey2(2)->findOne();
        $this->assertTrue($Multiplepk == null, 'multipleuniquekey findPK clear cache after delete');
    }

    public function testOneToOneRelationCacheSingle() {
        $this->simpleBuild('one_to_one_relation_single', "Objecttest");
        $Object1 = new \ObjecttestOnetoone1();
        $Object1->setId(1);
        $Object1->setKey1(2);
        $Object1->save();
        $Object2 = new \ObjecttestOnetoone2();
        $Object2->setId(1);
        $Object2->setKey1(2);
        $Object2->save();
        $Object2 = $Object1->getObjecttestOnetoone2RelatedById();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2, 'object1 get one to one object2 relative by id with no cache');
        $Object1 = \ObjecttestOnetoone1Query::create()->findOneById(1);
        $Object2 = $Object1->getObjecttestOnetoone2RelatedById();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by id with cache');
        $Object2 = $Object1->getObjecttestOnetoone2RelatedByKey1();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2, 'object1 get one to one object2 relative by key1 with no cache');
        $Object1 = \ObjecttestOnetoone1Query::create()->findOneById(1);
        $Object2 = $Object1->getObjecttestOnetoone2RelatedByKey1();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by key1 with cache');
        $Object2->setValue(2);
        $Object2->save();
        $Object1 = \ObjecttestOnetoone1Query::create()->findOneById(1);
        $Object2 = $Object1->getObjecttestOnetoone2RelatedById();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2, 'object1 get one to one object2 relative by id clear cache after save');
        $Object1 = \ObjecttestOnetoone1Query::create()->findOneById(1);
        $Object2 = $Object1->getObjecttestOnetoone2RelatedByKey1();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2, 'object1 get one to one object2 relative by key1 clear cache after save');
        $Object1 = \ObjecttestOnetoone1Query::create()->findOneById(1);
        $Object2->delete();
        $Object2 = $Object1->getObjecttestOnetoone2RelatedById();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by id clear cache after delete');
        $Object1 = \ObjecttestOnetoone1Query::create()->findOneById(1);
        $Object2 = $Object1->getObjecttestOnetoone2RelatedByKey1();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by key1 clear cache after delete');
    }

    public function testOneToOneRelationCacheMultiple() {
        $this->simpleBuild('one_to_one_relation_multiple', "Objecttest");
        $Object1 = new \ObjecttestOnetoone1Multiple();
        $Object1->setId1(1);
        $Object1->setId2(1);
        $Object1->setKey1(2);
        $Object1->setKey2(2);
        $Object1->save();
        $Object2 = new \ObjecttestOnetoone2Multiple();
        $Object2->setId1(1);
        $Object2->setId2(1);
        $Object2->setKey1(2);
        $Object2->setKey2(2);
        $Object2->save();
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedById1Id2();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2Multiple, 'object1 get one to one object2 relative by id with no cache');
        $Object1 = \ObjecttestOnetoone1MultipleQuery::create()->findPk(array(1, 1));
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedById1Id2();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by id with cache');
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedByKey1Key2();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2Multiple, 'object1 get one to one object2s relative by key1 with no cache');
        $Object1 = \ObjecttestOnetoone1MultipleQuery::create()->findPk(array(1, 1));
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedByKey1Key2();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by key1 with cache');
        $Object2->setValue(2);
        $Object2->save();
        $Object1 = \ObjecttestOnetoone1MultipleQuery::create()->findPk(array(1, 1));
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedById1Id2();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2Multiple, 'object1 get one to one object2 relative by id clear cache after save');
        $Object1 = \ObjecttestOnetoone1MultipleQuery::create()->findPk(array(1, 1));
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedByKey1Key2();
        $this->assertTrue($Object2 instanceof \ObjecttestOnetoone2Multiple, 'object1 get one to one object2 relative by key1 clear cache after save');
        $Object1 = \ObjecttestOnetoone1MultipleQuery::create()->findPk(array(1, 1));
        $Object2->delete();
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedById1Id2();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by id clear cache after delete');
        $Object1 = \ObjecttestOnetoone1MultipleQuery::create()->findPk(array(1, 1));
        $Object2 = $Object1->getObjecttestOnetoone2MultipleRelatedByKey1Key2();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by key1 clear cache after delete');
    }

    public function testOneToManyRelationCacheSingle() {
        $this->simpleBuild('one_to_many_relation_single', "Objecttest");
        $Object1 = new \ObjecttestOnetomany1();
        $Object1->setId(1);
        $Object1->setKey1(2);
        $Object1->save();
        $Object2 = new \ObjecttestOnetomany2();
        $Object2->setId(1);
        $Object2->setKey1(2);
        $Object2->save();
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertInstanceOf('\\ObjecttestOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get one to many object2 relative by key1 with no cache');

        $Object1 = \ObjecttestOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with cache');
        $this->assertTrue($Object1->countObjecttestOnetomany2s() instanceof MockContainer, 'object1 count one to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestOnetomany2s(), 1, 'object1 count one to many object2 relative by key1 with cache');

        // save an object will clear reference cache but keep count cache.
        $Object2->setValue(1);
        $Object2->save();
        $Object1 = \ObjecttestOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($Object1->countObjecttestOnetomany2s() instanceof MockContainer, 'object1 count one to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestOnetomany2s(), 1, 'object1 count one to many object2 relative by key1 with cache');

        // insert an object will clear all cache.
        $Object3 = new \ObjecttestOnetomany2();
        $Object3->setId(2);
        $Object3->setKey1(2);
        $Object3->save();
        $Object1 = \ObjecttestOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();
        $this->assertEquals(count($Object2s), 2, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 2, 'object1 get one to many object2 relative by key1 with no cache');

        // delete an object will clear all cache.
        $Object3->delete();
        $Object1 = \ObjecttestOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get one to many object2 relative by key1 with no cache');
    }

    public function testOneToManyRelationCacheMultiple() {
        $this->simpleBuild('one_to_many_relation_multiple', "ObjecttestMultiple");
        $Object1 = new \ObjecttestMultipleOnetomany1();
        $Object1->setId(1);
        $Object1->setKey1(1);
        $Object1->setKey2(2);
        $Object1->save();
        $Object2 = new \ObjecttestMultipleOnetomany2();
        $Object2->setId(1);
        $Object2->setKey2(1);
        $Object2->setKey1(2);
        $Object2->save();

        $Object2s = $Object1->getObjecttestMultipleOnetomany2s();
        $CountObject2s = $Object1->countObjecttestMultipleOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertInstanceOf('\\ObjecttestMultipleOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get one to many object2 relative by key1 with no cache');

        $Object1 = \ObjecttestMultipleOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with cache');
        $this->assertTrue($Object1->countObjecttestMultipleOnetomany2s() instanceof MockContainer, 'object1 count one to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestMultipleOnetomany2s(), 1, 'object1 count one to many object2 relative by key1 with cache');

        // save an object will clear reference cache but keep count cache.
        $Object2->setValue(1);
        $Object2->save();
        $Object1 = \ObjecttestMultipleOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($Object1->countObjecttestMultipleOnetomany2s() instanceof MockContainer, 'object1 count one to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestMultipleOnetomany2s(), 1, 'object1 count one to many object2 relative by key1 with cache');

        // insert an object will clear all cache.
        $Object3 = new \ObjecttestMultipleOnetomany2();
        $Object3->setId(2);
        $Object3->setKey2(1);
        $Object3->setKey1(2);
        $Object3->save();
        $Object1 = \ObjecttestMultipleOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleOnetomany2s();
        $CountObject2s = $Object1->countObjecttestMultipleOnetomany2s();
        $this->assertEquals(count($Object2s), 2, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 2, 'object1 get one to many object2 relative by key1 with no cache');

        // delete an object will clear all cache.
        $Object3->delete();
        $Object1 = \ObjecttestMultipleOnetomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleOnetomany2s();
        $CountObject2s = $Object1->countObjecttestMultipleOnetomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get one to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get one to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleOnetomany2', $Object2s[0], 'object1 get one to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get one to many object2 relative by key1 with no cache');
    }
*/
    public function testManyToManyRelationCacheSingle() {
        $this->simpleBuild('many_to_many_relation_single', "Objecttest");        
        $Object1 = new \ObjecttestManytomany1();
        $Object1->setId(1);
        $Object1->setTable1key(1);        
        $Object2 = new \ObjecttestManytomany2();
        $Object2->setId(2);
        $Object2->setTable2key(2);                        
        $Object1->addObjecttestManytomany2($Object2);
        $Object1->save();

        $Object1 = \ObjecttestManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestManytomany2s();
        $CountObject2s = $Object1->countObjecttestManytomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertInstanceOf('\\ObjecttestManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get many to many object2 relative by key1 with no cache');
 
        $Object1 = \ObjecttestManytomany1Query::create()->findPk(1);        
        $Object2s = $Object1->getObjecttestManytomany2s();        
        $this->assertEquals(count($Object2s), 1, 'count object1 get manyn to many object2 relative by key1 with no cache');
        $this->assertTrue($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with cache');
        $this->assertTrue($Object1->countObjecttestManytomany2s() instanceof MockContainer, 'object1 count many to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestManytomany2s(), 1, 'object1 count many to many object2 relative by key1 with cache');

        // save an object will clear reference cache but keep count cache.
        $Object2->setValue(1);
        $Object2->save();
        $Object1 = \ObjecttestManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestManytomany2s();        
        $this->assertEquals(count($Object2s), 1, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($Object1->countObjecttestManytomany2s() instanceof MockContainer, 'object1 count many to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestManytomany2s(), 1, 'object1 count many to many object2 relative by key1 with cache');

        // insert an object will clear all cache.
        $Object3 = new \ObjecttestManytomany2();
        $Object3->setId(3);
        $Object3->setTable2key(3);
        $Object1->addObjecttestManytomany2($Object3);
        $Object1->save();
        
        $Object1 = \ObjecttestManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestManytomany2s();
        $CountObject2s = $Object1->countObjecttestManytomany2s();        
        $this->assertEquals(count($Object2s), 2, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 2, 'object1 get many to many object2 relative by key1 with no cache');

        // delete an object will clear all cache.
        $Object3->delete();
        $Object1 = \ObjecttestManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestManytomany2s();
        $CountObject2s = $Object1->countObjecttestManytomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get many to many object2 relative by key1 with no cache');
    }

    public function testManyToManyRelationCacheMultiple() {        
        $this->simpleBuild('many_to_many_relation_multiple', "ObjecttestMultiple");        
        $Object1 = new \ObjecttestMultipleManytomany1();
        $Object1->setId(1);
        $Object1->setTable1key1(11);
        $Object1->setTable1key2(12);        
        $Object2 = new \ObjecttestMultipleManytomany2();
        $Object2->setId(2);
        $Object2->setTable2key1(21);
        $Object2->setTable2key2(22);        
        $Object1->addObjecttestMultipleManytomany2($Object2);
        $Object1->save();

        $Object1 = \ObjecttestMultipleManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleManytomany2s();
        $CountObject2s = $Object1->countObjecttestMultipleManytomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertInstanceOf('\\ObjecttestMultipleManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get many to many object2 relative by key1 with no cache');
 
        $Object1 = \ObjecttestMultipleManytomany1Query::create()->findPk(1);        
        $Object2s = $Object1->getObjecttestMultipleManytomany2s();        
        $this->assertEquals(count($Object2s), 1, 'count object1 get manyn to many object2 relative by key1 with no cache');
        $this->assertTrue($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with cache');
        $this->assertTrue($Object1->countObjecttestMultipleManytomany2s() instanceof MockContainer, 'object1 count many to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestMultipleManytomany2s(), 1, 'object1 count many to many object2 relative by key1 with cache');

        // save an object will clear reference cache but keep count cache.
        $Object2->setValue(1);
        $Object2->save();
        $Object1 = \ObjecttestMultipleManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleManytomany2s();        
        $this->assertEquals(count($Object2s), 1, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($Object1->countObjecttestMultipleManytomany2s() instanceof MockContainer, 'object1 count many to many object2 relative by key1 with cache');
        $this->assertEquals((int) (string) $Object1->countObjecttestMultipleManytomany2s(), 1, 'object1 count many to many object2 relative by key1 with cache');

        // insert an object will clear all cache.
        $Object3 = new \ObjecttestMultipleManytomany2();
        $Object3->setId(3);
        $Object3->setTable2key1(31);
        $Object3->setTable2key2(32);
        $Object1->addObjecttestMultipleManytomany2($Object3);
        $Object1->save();

        $Object1 = \ObjecttestMultipleManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleManytomany2s();
        $CountObject2s = $Object1->countObjecttestMultipleManytomany2s();        
        $this->assertEquals(count($Object2s), 2, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 2, 'object1 get many to many object2 relative by key1 with no cache');

        // delete an object will clear all cache.
        $Object3->delete();
        $Object1 = \ObjecttestMultipleManytomany1Query::create()->findPk(1);
        $Object2s = $Object1->getObjecttestMultipleManytomany2s();
        $CountObject2s = $Object1->countObjecttestMultipleManytomany2s();
        $this->assertEquals(count($Object2s), 1, 'count object1 get many to many object2 relative by key1 with no cache');
        $this->assertFalse($Object2s instanceof MockContainer, 'object1 get many to many object2 relative by key1 with cache');
        $this->assertInstanceOf('\\ObjecttestMultipleManytomany2', $Object2s[0], 'object1 get many to many object2 relative by key1 with no cache');
        $this->assertTrue($CountObject2s === 1, 'object1 get many to many object2 relative by key1 with no cache');
    }  
    
}