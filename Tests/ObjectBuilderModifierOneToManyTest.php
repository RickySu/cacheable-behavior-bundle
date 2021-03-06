<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierOneToManyTest extends Base
{
    public function setup()
    {
        $this->prepareMockTagcache();
    }

    public function DataProvider_OneToManyRelationSingle()
    {
        $this->simpleBuild('one_to_many_relation_single', "Objecttest");
        $ClassName = '\\ObjecttestOnetomany';
        for ($i = 0; $i < 5; $i++) {
            $OriginObject2s = array();
            $ModifyObject2s = array();
            for ($j = 0; $j < 5; $j++) {
                $OriginObject2s[] = array(
                    'Id' => $i * 10 + $j,
                    'Key' => $i + 20,
                    'Value' => \rand(),
                );
                $ModifyObject2s[] = array(
                    'Id' => $i * 10 + $j,
                    'Key' => $i + 20,
                    'Value' => \rand(),
                );
            }
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Object1' => array(
                        'Id' => $i,
                        'Key' => $i + 20,
                        'Value' => \rand(),
                    ),
                    'Object2s' => $OriginObject2s,
                ),
                'ModifyData' => array(
                    'Object1' => array(
                        'Id' => $i,
                        'Key' => $i + 20,
                        'Value' => \rand(),
                    ),
                    'Object2s' => $ModifyObject2s,
                ),
            );
        }

        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_OneToManyRelationSingle
     */
    public function testCacheClearOneToManyRelationSingle($ClassName, $OriginData, $ModifyData)
    {
        $ObjectClass1 = "{$ClassName}1";
        $QueryClass1 = "{$ClassName}1Query";
        $ObjectClass2 = "{$ClassName}2";
        $ObjectClass2Peer = "{$ClassName}2Peer";
        $QueryClass2 = "{$ClassName}2Query";
        $getMethod = str_replace('\\', '', "get{$ClassName}2s");
        $countMethod = str_replace('\\', '', "count{$ClassName}2s");

        $Object1 = new $ObjectClass1();
        $Object1->fromArray($OriginData['Object1']);
        $Object1->save();

        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        foreach ($OriginData['Object2s'] as $OriginalObject2) {
            $Object2 = new $ObjectClass2();
            $Object2->fromArray($OriginalObject2);
            $Object2->save();
        }
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();

        foreach ($OriginData['Object2s'] as $Index => $OriginalObject2) {
            $this->assertEquals($OriginalObject2, $Object2s[$Index]->toArray());
        }
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s === count($OriginData['Object2s']));

        // cache hit
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        foreach ($OriginData['Object2s'] as $Index => $OriginalObject2) {
            $this->assertEquals($OriginalObject2, $Object2s[$Index]->toArray());
        }
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));

        // save object will clear reference cache but keep count cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = $QueryClass2::create()->findPk($ModifyDataObject2['Id']);
            $Object2->fromArray($ModifyDataObject2);
            $Object2->save();
        }
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        foreach ($ModifyData['Object2s'] as $Index => $ModifyDataObject2) {
            $this->assertEquals($ModifyDataObject2, $Object2s[$Index]->toArray());
        }
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));

        // delete an object will clear all cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = $QueryClass2::create()->findPk($ModifyDataObject2['Id']);
            $Object2->delete();
        }
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        $this->assertEquals(count($Object2s), 0);
        $this->assertEquals($CountObject2s, 0);

        // insert an object will clear all cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = new $ObjectClass2();
            $Object2->fromArray($ModifyDataObject2);
            $Object2->save();
        }
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        foreach ($ModifyData['Object2s'] as $Index => $ModifyDataObject2) {
            $this->assertEquals($ModifyDataObject2, $Object2s[$Index]->toArray());
        }
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));

        // with criteria no cache
        $Object2s = $Object1->$getMethod();
        $Object2 = $Object2s[0];
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $Criteria->add($ObjectClass2Peer::KEY, null, \Criteria::ISNOTNULL);
        $Object2s = $Object1->$getMethod($Criteria);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $Criteria->add($ObjectClass2Peer::KEY, null, \Criteria::ISNOTNULL);
        $CountObject2s = $Object1->$countMethod($Criteria);
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        $this->assertEquals($CountObject2s, 1);
        $this->assertEquals($Object2->toArray(), $Object2s[0]->toArray());

        // with criteria and cache hit
        $Object2s = $Object1->$getMethod();
        $Object2 = $Object2s[0];
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::KEY, null, \Criteria::ISNOTNULL);
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $Object2s = $Object1->$getMethod($Criteria);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::KEY, null, \Criteria::ISNOTNULL);
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $CountObject2s = $Object1->$countMethod($Criteria);
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $this->assertEquals((int) (string) $CountObject2s, 1);
        $this->assertEquals($Object2->toArray(), $Object2s[0]->toArray());

        //change relation
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $Object2 = $Object2s[0];
        $newObject1 = new $ObjectClass1();
        $newObject1->fromArray($OriginData['Object1']);
        $newObject1->setId($newObject1->getId()+9999);
        $newObject1->setKey($newObject1->getKey()+9999);
        $newObject1->save();
        $setObject1Method=str_replace('\\', '', "set{$ObjectClass1}");
        $Object2->$setObject1Method($newObject1);
        $Object2->save();
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
    }

    public function DataProvider_OneToManyRelationMultiple()
    {
        $this->simpleBuild('one_to_many_relation_multiple', "Objecttest");
        $ClassName = '\\ObjecttestOnetomanyMultiple';
        for ($i = 0; $i < 5; $i++) {
            $OriginObject2s = array();
            $ModifyObject2s = array();
            for ($j = 0; $j < 5; $j++) {
                $OriginObject2s[] = array(
                    'Id' => $i * 10 + $j,
                    'Key2' => $i + 10,
                    'Key1' => $i + 20,
                    'Value' => \rand(),
                );
                $ModifyObject2s[] = array(
                    'Id' => $i * 10 + $j,
                    'Key2' => $i + 10,
                    'Key1' => $i + 20,
                    'Value' => \rand(),
                );
            }
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Object1' => array(
                        'Id' => $i,
                        'Key1' => $i + 10,
                        'Key2' => $i + 20,
                        'Value' => \rand(),
                    ),
                    'Object2s' => $OriginObject2s,
                ),
                'ModifyData' => array(
                    'Object1' => array(
                        'Id' => $i,
                        'Key1' => $i + 10,
                        'Key2' => $i + 20,
                        'Value' => \rand(),
                    ),
                    'Object2s' => $ModifyObject2s,
                ),
            );
        }

        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_OneToManyRelationMultiple
     */
    public function testCacheClearOneToManyRelationMultiple($ClassName, $OriginData, $ModifyData)
    {
        $ObjectClass1 = "{$ClassName}1";
        $QueryClass1 = "{$ClassName}1Query";
        $ObjectClass2 = "{$ClassName}2";
        $ObjectClass2Peer = "{$ClassName}2Peer";
        $QueryClass2 = "{$ClassName}2Query";
        $getMethod = str_replace('\\', '', "get{$ClassName}2s");
        $countMethod = str_replace('\\', '', "count{$ClassName}2s");

        $Object1 = new $ObjectClass1();
        $Object1->fromArray($OriginData['Object1']);
        $Object1->save();

        foreach ($OriginData['Object2s'] as $OriginalObject2) {
            $Object2 = new $ObjectClass2();
            $Object2->fromArray($OriginalObject2);
            $Object2->save();
        }

        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        foreach ($OriginData['Object2s'] as $Index => $OriginalObject2) {
            $this->assertEquals($OriginalObject2, $Object2s[$Index]->toArray());
        }
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s === count($OriginData['Object2s']));

        //cache hit
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        foreach ($OriginData['Object2s'] as $Index => $OriginalObject2) {
            $this->assertEquals($OriginalObject2, $Object2s[$Index]->toArray());
        }
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));

        // save object will clear reference cache but keep count cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = $QueryClass2::create()->findPk($ModifyDataObject2['Id']);
            $Object2->fromArray($ModifyDataObject2);
            $Object2->save();
        }
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        foreach ($ModifyData['Object2s'] as $Index => $ModifyDataObject2) {
            $this->assertEquals($ModifyDataObject2, $Object2s[$Index]->toArray());
        }
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));

        // delete an object will clear all cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = $QueryClass2::create()->findPk($ModifyDataObject2['Id']);
            $Object2->delete();
        }
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        $this->assertEquals(count($Object2s), 0);
        $this->assertEquals($CountObject2s, 0);

        // insert an object will clear all cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = new $ObjectClass2();
            $Object2->fromArray($ModifyDataObject2);
            $Object2->save();
        }
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        foreach ($ModifyData['Object2s'] as $Index => $ModifyDataObject2) {
            $this->assertEquals($ModifyDataObject2, $Object2s[$Index]->toArray());
        }
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));

        // with criteria no cache
        $Object2s = $Object1->$getMethod();
        $Object2 = $Object2s[0];
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $Criteria->add($ObjectClass2Peer::KEY1, null, \Criteria::ISNOTNULL);
        $Object2s = $Object1->$getMethod($Criteria);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $Criteria->add($ObjectClass2Peer::KEY1, null, \Criteria::ISNOTNULL);
        $CountObject2s = $Object1->$countMethod($Criteria);
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        $this->assertEquals($CountObject2s, 1);
        $this->assertEquals($Object2->toArray(), $Object2s[0]->toArray());

        // with criteria and cache hit
        $Object2s = $Object1->$getMethod();
        $Object2 = $Object2s[0];
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::KEY1, null, \Criteria::ISNOTNULL);
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $Object2s = $Object1->$getMethod($Criteria);
        $Criteria = new \Criteria();
        $Criteria->add($ObjectClass2Peer::KEY1, null, \Criteria::ISNOTNULL);
        $Criteria->add($ObjectClass2Peer::ID, $Object2->getId());
        $CountObject2s = $Object1->$countMethod($Criteria);
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $this->assertEquals((int) (string) $CountObject2s, 1);
        $this->assertEquals($Object2->toArray(), $Object2s[0]->toArray());

        //change relation
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $Object2 = $Object2s[0];
        $newObject1 = new $ObjectClass1();
        $newObject1->fromArray($OriginData['Object1']);
        $newObject1->setId($newObject1->getId()+9999);
        $newObject1->setKey1($newObject1->getKey1()+9999);
        $newObject1->setKey2($newObject1->getKey2()+9999);
        $newObject1->save();
        $setObject1Method=str_replace('\\', '', "set{$ObjectClass1}");
        $Object2->$setObject1Method($newObject1);
        $Object2->save();
        $Object2s = $Object1->$getMethod();
        $CountObject2s = $Object1->$countMethod();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
    }

}
