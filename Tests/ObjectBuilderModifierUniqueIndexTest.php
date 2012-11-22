<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class ObjectBuilderModifierUniqueIndexTest extends Base {

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
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Key' => $i,
                    'Value' => $i,
                ),
                'ModifyData' => array(
                    'Id' => $i,
                    'Key' => $i,
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
        $Object = $QueryClass::create()->findOneByKey($OriginData['Key']);
        $Object = $QueryClass::create()->findOneByKey($OriginData['Key']);
        $this->assertTrue($Object instanceof MockContainer, 'single unique index with cache hit');
        $this->assertEquals($Object->toArray(), $OriginData);
        $Object->fromArray($ModifyData);
        $Object->save();
        $Object = $QueryClass::create()->findOneByKey($OriginData['Key']);
        $this->assertTrue($Object instanceof $ObjectClass, 'single unique index clear cache after save');
        $this->assertEquals($Object->toArray(), $ModifyData);
        $Object->delete();
        $Object = $QueryClass::create()->findOneByKey($OriginData['Key']);
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
    
}