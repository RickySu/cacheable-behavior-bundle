<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierTest extends Base {

    public function setup() {
        $this->prepareMockTagcache();
    }

    public function DataProvider_SinglePrimaryKey() {
        $this->simpleBuild('singleprimarykey', "Objecttest");
        $ClassName = '\\ObjecttestSinglepk';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Value' => $i,
                ),
                'ModifyData' => array(
                    'Id' => $i,
                    'Value' => \rand(),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_SinglePrimaryKey
     */
    public function testCacheClearSinglePrimaryKey($ClassName, $OriginData, $ModifyData) {
        $ObjectClass = $ClassName;
        $QueryClass = "{$ClassName}Query";
        $Object = new $ObjectClass();
        $Object->fromArray($OriginData);
        $Object->save();
        $Object = $QueryClass::create()->findPk($OriginData['Id']);
        $Object = $QueryClass::create()->findPk($OriginData['Id']);
        $this->assertTrue($Object instanceof MockContainer, 'singlepk findPK with cache hit');
        $this->assertEquals($Object->toArray(), $OriginData);
        $Object->fromArray($ModifyData);
        $Object->save();
        $Object = $QueryClass::create()->findPk($OriginData['Id']);
        $this->assertTrue($Object instanceof $ObjectClass, 'singlepk findPK clear cache after save');
        $this->assertEquals($Object->toArray(), $ModifyData);
        $Object->delete();
        $Object = $QueryClass::create()->findPk($OriginData['Id']);
        $this->assertTrue($Object == null, 'singlepk findPK clear cache after delete');
    }

    public function DataProvider_MultiplePrimaryKey() {
        $this->simpleBuild('multipleprimarykey', "Objecttest");
        $ClassName = '\\ObjecttestMultiplepk';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id1' => $i,
                    'Id2' => $i,
                    'Value' => $i,
                ),
                'ModifyData' => array(
                    'Id1' => $i,
                    'Id2' => $i,
                    'Value' => \rand(),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_MultiplePrimaryKey
     */
    public function testCacheClearMultiplePrimaryKey($ClassName, $OriginData, $ModifyData) {
        $ObjectClass = $ClassName;
        $QueryClass = "{$ClassName}Query";
        $Object = new $ObjectClass();
        $Object->fromArray($OriginData);
        $Object->save();
        $Object = $QueryClass::create()->findPk(array($OriginData['Id1'], $OriginData['Id2']));
        $Object = $QueryClass::create()->findPk(array($OriginData['Id1'], $OriginData['Id2']));
        $this->assertTrue($Object instanceof MockContainer, 'multiple findPK with cache hit');
        $this->assertEquals($Object->toArray(), $OriginData);
        $Object->fromArray($ModifyData);
        $Object->save();
        $Object = $QueryClass::create()->findPk(array($OriginData['Id1'], $OriginData['Id2']));
        $this->assertTrue($Object instanceof $ObjectClass, 'multiple findPK clear cache after save');
        $this->assertEquals($Object->toArray(), $ModifyData);
        $Object->delete();
        $Object = $QueryClass::create()->findPk(array($OriginData['Id1'], $OriginData['Id2']));
        $this->assertTrue($Object == null, 'multiple findPK clear cache after delete');
    }

    public function DataProvider_SingleUniqueKey() {
        $this->simpleBuild('singleuniquekey', "Objecttest");
        $ClassName = '\\ObjecttestSingleuniquekey';
        for ($i = 0; $i < 1; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Key1' => $i,
                    'Value' => $i,
                ),
                'ModifyData' => array(
                    'Id' => $i,
                    'Key1' => $i,
                    'Value' => \rand(),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_SingleUniqueKey
     */
    public function testCacheClearSingleUniqueKey($ClassName, $OriginData, $ModifyData) {
        $ObjectClass = $ClassName;
        $QueryClass = "{$ClassName}Query";
        $Object = new $ObjectClass();
        $Object->fromArray($OriginData);
        $Object->save();
        $Object = $QueryClass::create()->findOneByKey1($OriginData['Key1']);
        $Object = $QueryClass::create()->findOneByKey1($OriginData['Key1']);
        $this->assertTrue($Object instanceof MockContainer, 'single unique index with cache hit');
        $this->assertEquals($Object->toArray(), $OriginData);
        $Object->fromArray($ModifyData);
        $Object->save();
        $Object = $QueryClass::create()->findOneByKey1($OriginData['Key1']);
        $this->assertTrue($Object instanceof $ObjectClass, 'single unique index clear cache after save');
        $this->assertEquals($Object->toArray(), $ModifyData);
        $Object->delete();
        $Object = $QueryClass::create()->findOneByKey1($OriginData['Key1']);
        $this->assertTrue($Object == null, 'single unique index clear cache after delete');
    }

    public function DataProvider_MultipleUniqueKey() {
        $this->simpleBuild('multipleuniquekey', "Objecttest");
        $ClassName = '\\ObjecttestMultipleuniquekey';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Key1' => $i + 10,
                    'Key2' => $i + 20,
                    'Value' => $i,
                ),
                'ModifyData' => array(
                    'Id' => $i,
                    'Key1' => $i + 10,
                    'Key2' => $i + 20,
                    'Value' => \rand(),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_MultipleUniqueKey
     */
    public function testCacheClearMultipleUniqueKey($ClassName, $OriginData, $ModifyData) {
        $ObjectClass = $ClassName;
        $QueryClass = "{$ClassName}Query";
        $Object = new $ObjectClass();
        $Object->fromArray($OriginData);
        $Object->save();
        $Object = $QueryClass::create()->filterByKey1($OriginData['Key1'])->filterByKey2($OriginData['Key2'])->findOne();
        $Object = $QueryClass::create()->filterByKey1($OriginData['Key1'])->filterByKey2($OriginData['Key2'])->findOne();
        $this->assertTrue($Object instanceof MockContainer, 'multipleuniquekey findOne with cache hit');
        $this->assertEquals($Object->toArray(), $OriginData);
        $Object->fromArray($ModifyData);
        $Object->save();
        $Object = $QueryClass::create()->filterByKey1($OriginData['Key1'])->filterByKey2($OriginData['Key2'])->findOne();
        $this->assertTrue($Object instanceof $ObjectClass, 'multipleuniquekey findOne clear cache after save');
        $this->assertEquals($Object->toArray(), $ModifyData);
        $Object->delete();
        $Object = $QueryClass::create()->filterByKey1($OriginData['Key1'])->filterByKey2($OriginData['Key2'])->findOne();
        $this->assertTrue($Object == null, 'multipleuniquekey findOne clear cache after delete');
    }

    public function DataProvider_OneToOneRelationSingle() {
        $this->simpleBuild('one_to_one_relation_single', "Objecttest");
        $ClassName = '\\ObjecttestOnetoone';
        for ($i = 0; $i < 1; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Object1' => array(
                        'Id' => $i,
                        'Key1' => $i + 10,
                        'Value' => \rand(),
                    ),
                    'Object2' => array(
                        'Id' => $i,
                        'Key1' => $i + 10,
                        'Value' => \rand(),
                    ),
                ),
                'ModifyData' => array(
                    'Object1' => array(
                        'Id' => $i,
                        'Key1' => $i + 10,
                        'Value' => \rand(),
                    ),
                    'Object2' => array(
                        'Id' => $i,
                        'Key1' => $i + 10,
                        'Value' => \rand(),
                    ),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_OneToOneRelationSingle
     */
    public function testCacheClearOneToOneRelationSingle($ClassName, $OriginData, $ModifyData) {
        $ObjectClass1 = "{$ClassName}1";
        $QueryClass1 = "{$ClassName}1Query";
        $ObjectClass2 = "{$ClassName}2";
        $QueryClass2 = "{$ClassName}2Query";
        $MethodById = str_replace('\\', '', "get{$ClassName}2RelatedById");
        $MethodByKey1 = str_replace('\\', '', "get{$ClassName}2RelatedByKey1");
        $Object1 = new $ObjectClass1();
        $Object1->fromArray($OriginData['Object1']);
        $Object1->save();
        $Object2 = new $ObjectClass2();
        $Object2->fromArray($OriginData['Object2']);
        $Object2->save();

        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by id with no cache');
        $Object1 = $QueryClass1::create()->findOneById($OriginData['Object1']['Id']);
        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by id with cache');
        $this->assertEquals($Object2->toArray(), $OriginData['Object2']);
        $Object2 = $Object1->$MethodByKey1();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by key1 with no cache');
        $Object1 = $QueryClass1::create()->findOneById($OriginData['Object1']['Id']);
        $Object2 = $Object1->$MethodByKey1();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by key1 with cache');
        $this->assertEquals($Object2->toArray(), $OriginData['Object2']);

        $Object2->fromArray($ModifyData['Object2']);
        $Object2->save();

        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by id with no cache');
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by id with cache');
        $this->assertEquals($Object2->toArray(), $ModifyData['Object2']);
        $Object2 = $Object1->$MethodByKey1();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by key1 with no cache');
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2 = $Object1->$MethodByKey1();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by key1 with cache');
        $this->assertEquals($Object2->toArray(), $ModifyData['Object2']);

        $Object2->delete();
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by id clear cache after delete');
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2 = $Object1->$MethodByKey1();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by key1 clear cache after delete');
    }

    public function DataProvider_OneToOneRelationMultiple() {
        $this->simpleBuild('one_to_one_relation_multiple', "Objecttest");
        $ClassName = '\\ObjecttestOnetooneMultiple';
        for ($i = 0; $i < 1; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Object1' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                    'Object2' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                ),
                'ModifyData' => array(
                    'Object1' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                    'Object2' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_OneToOneRelationMultiple
     */
    public function testCacheClearOneToOneRelationMultiple($ClassName, $OriginData, $ModifyData) {
        $ObjectClass1 = "{$ClassName}1";
        $QueryClass1 = "{$ClassName}1Query";
        $ObjectClass2 = "{$ClassName}2";
        $QueryClass2 = "{$ClassName}2Query";
        $MethodById = str_replace('\\', '', "get{$ClassName}2RelatedById1Id2");
        $MethodByKey = str_replace('\\', '', "get{$ClassName}2RelatedByKey1Key2");
        $Object1 = new $ObjectClass1();
        $Object1->fromArray($OriginData['Object1']);
        $Object1->save();
        $Object2 = new $ObjectClass2();
        $Object2->fromArray($OriginData['Object2']);
        $Object2->save();

        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by id with no cache');
        $Object1 = $QueryClass1::create()->findPk(array($OriginData['Object1']['Id1'], $OriginData['Object1']['Id2']));
        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by id with cache');
        $this->assertEquals($Object2->toArray(), $OriginData['Object2']);
        $Object2 = $Object1->$MethodByKey();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by key1 with no cache');
        $Object1 = $QueryClass1::create()->findPk(array($OriginData['Object1']['Id1'], $OriginData['Object1']['Id2']));
        $Object2 = $Object1->$MethodByKey();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by key1 with cache');
        $this->assertEquals($Object2->toArray(), $OriginData['Object2']);

        $Object2->fromArray($ModifyData['Object2']);
        $Object2->save();

        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by id with no cache');
        $Object1 = $QueryClass1::create()->findPk(array($OriginData['Object1']['Id1'], $OriginData['Object1']['Id2']));
        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by id with cache');
        $this->assertEquals($Object2->toArray(), $ModifyData['Object2']);
        $Object2 = $Object1->$MethodByKey();
        $this->assertTrue($Object2 instanceof $ObjectClass2, 'object1 get one to one object2 relative by key1 with no cache');
        $Object1 = $QueryClass1::create()->findPk(array($OriginData['Object1']['Id1'], $OriginData['Object1']['Id2']));
        $Object2 = $Object1->$MethodByKey();
        $this->assertTrue($Object2 instanceof MockContainer, 'object1 get one to one object2 relative by key1 with cache');
        $this->assertEquals($Object2->toArray(), $ModifyData['Object2']);

        $Object2->delete();
        $Object1 = $QueryClass1::create()->findPk(array($OriginData['Object1']['Id1'], $OriginData['Object1']['Id2']));
        $Object2 = $Object1->$MethodById();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by id clear cache after delete');
        $Object1 = $QueryClass1::create()->findPk(array($OriginData['Object1']['Id1'], $OriginData['Object1']['Id2']));
        $Object2 = $Object1->$MethodByKey();
        $this->assertTrue($Object2 == null, 'object1 get one to one object2 relative by key1 clear cache after delete');
    }

    public function DataProvider_OneToManyRelationSingle() {
        $this->simpleBuild('one_to_many_relation_single', "Objecttest");
        $ClassName = '\\ObjecttestOnetomany';
        for ($i = 0; $i < 1; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Object1' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                    'Object2' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                ),
                'ModifyData' => array(
                    'Object1' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                    'Object2' => array(
                        'Id1' => $i + 10,
                        'Id2' => $i + 20,
                        'Key1' => $i + 30,
                        'Key2' => $i + 40,
                        'Value' => \rand(),
                    ),
                ),
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_OneToManyRelationSingle
     */    
    public function testCacheClearOneToManyRelationSingle() {
        return;
        $ObjectClass1 = "{$ClassName}1";
        $QueryClass1 = "{$ClassName}1Query";
        $ObjectClass2 = "{$ClassName}2";
        $QueryClass2 = "{$ClassName}2Query";
        $getMethod = str_replace('\\', '', "get{$ClassName}2s");
        $countMethod = str_replace('\\', '', "count{$ClassName}2s");

        $Object1 = new $ObjectClass1();
        $Object1->fromArray();
        $Object1->save();
        $Object1 = new $ObjectClass2();
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
        return;
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

    public function testManyToManyRelationCacheSingle() {
        return;
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
        return;
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