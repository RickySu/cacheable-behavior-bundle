<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierTest extends Base {

    public function setup() {
        $this->prepareMockTagcache();
    }

    public function DataProvider_OneToManyRelationSingle() {
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
    public function testCacheClearOneToManyRelationSingle($ClassName, $OriginData, $ModifyData) {
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
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();

        foreach ($OriginData['Object2s'] as $Index => $OriginalObject2) {
            $this->assertEquals($OriginalObject2, $Object2s[$Index]->toArray());            
        }
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s === count($OriginData['Object2s']));
       
        // save object will clear reference cache but keep count cache.
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = $QueryClass2::create()->findPk($ModifyDataObject2['Id']);
            $Object2->fromArray($ModifyDataObject2);
            $Object2->save();
        }        
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();
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
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        $this->assertEquals(count($Object2s),0);
        $this->assertEquals($CountObject2s,0);

        // insert an object will clear all cache.        
        foreach ($ModifyData['Object2s'] as $ModifyDataObject2) {
            $Object2 = new $ObjectClass2();
            $Object2->fromArray($ModifyDataObject2);
            $Object2->save();
        }        
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $CountObject2s = $Object1->countObjecttestOnetomany2s();
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        foreach ($ModifyData['Object2s'] as $Index => $ModifyDataObject2) {
            $this->assertEquals($ModifyDataObject2, $Object2s[$Index]->toArray());
        }        
        $this->assertEquals((int) (string) $CountObject2s, count($ModifyData['Object2s']));
        
        // with criteria no cache
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $Object2=$Object2s[0];
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Criteria=new \Criteria();
        $Criteria->add($ObjectClass2Peer::ID,$Object2->getId());
        $Criteria->add($ObjectClass2Peer::KEY,null,\Criteria::ISNOTNULL);
        $Object2s = $Object1->getObjecttestOnetomany2s($Criteria);
        $Criteria=new \Criteria();
        $Criteria->add($ObjectClass2Peer::ID,$Object2->getId());
        $Criteria->add($ObjectClass2Peer::KEY,null,\Criteria::ISNOTNULL);        
        $CountObject2s = $Object1->countObjecttestOnetomany2s($Criteria);
        $this->assertFalse($Object2s instanceof MockContainer);
        $this->assertFalse($CountObject2s instanceof MockContainer);
        $this->assertEquals($CountObject2s,1);
        $this->assertEquals($Object2->toArray(),$Object2s[0]->toArray());

        // with criteria and cache hit
        $Object2s = $Object1->getObjecttestOnetomany2s();
        $Object2=$Object2s[0];
        $Object1 = $QueryClass1::create()->findPk($OriginData['Object1']['Id']);
        $Criteria=new \Criteria();
        $Criteria->add($ObjectClass2Peer::KEY,null,\Criteria::ISNOTNULL);
        $Criteria->add($ObjectClass2Peer::ID,$Object2->getId());        
        $Object2s = $Object1->getObjecttestOnetomany2s($Criteria);
        $Criteria=new \Criteria();
        $Criteria->add($ObjectClass2Peer::KEY,null,\Criteria::ISNOTNULL);
        $Criteria->add($ObjectClass2Peer::ID,$Object2->getId());                
        $CountObject2s = $Object1->countObjecttestOnetomany2s($Criteria);
        $this->assertTrue($Object2s instanceof MockContainer);
        $this->assertTrue($CountObject2s instanceof MockContainer);
        $this->assertEquals((int) (string)$CountObject2s,1);
        $this->assertEquals($Object2->toArray(),$Object2s[0]->toArray());
        
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

}