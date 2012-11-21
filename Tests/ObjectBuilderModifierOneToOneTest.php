<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierOneToOneTest extends Base {

    public function setup() {
        $this->prepareMockTagcache();
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

}