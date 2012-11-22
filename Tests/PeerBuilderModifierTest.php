<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Event\GetTagcacheEvent\GetTagcacheEvent;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class PeerBuilderModifierTest extends Base {

    public function setup() {
        $this->prepareMockTagcache();
    }

    public function DataProvider_SinglePK() {
        $this->simpleBuild('singleprimarykey', "Peertest");
        $ClassName = '\\PeertestSinglepk';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Value' => \rand(),
                ),
                'retrieveMethod' => 'retrieveByPk'
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_SinglePK
     */
    public function testSinglePK($ClassName, $Data, $retrieveMethod) {
        $PeerClass = "{$ClassName}Peer";
        $ObjectClass = $ClassName;
        $this->assertTrue(method_exists($PeerClass, $retrieveMethod));
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $PeerClass::$retrieveMethod($Data['Id']);
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $PeerClass::$retrieveMethod($Data['Id']);
        $this->assertTrue($Object instanceof MockContainer);
    }

    public function DataProvider_MultiplePK() {
        $this->simpleBuild('multipleprimarykey', "Peertest");
        $ClassName = '\\PeertestMultiplepk';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id1' => $i,
                    'Id2' => $i + 10,
                    'Value' => \rand(),
                ),
                'retrieveMethod' => 'retrieveByPk'
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_MultiplePK
     */
    public function testMultiplePK($ClassName, $Data, $retrieveMethod) {
        $PeerClass = "{$ClassName}Peer";
        $ObjectClass = $ClassName;
        $this->assertTrue(method_exists($PeerClass, $retrieveMethod));
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $PeerClass::$retrieveMethod($Data['Id1'], $Data['Id2']);
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $PeerClass::$retrieveMethod($Data['Id1'], $Data['Id2']);
        $this->assertTrue($Object instanceof MockContainer);
    }

    public function DataProvider_SingleUniqueKey() {
        $this->simpleBuild('singleuniquekey', "Peertest");
        $ClassName = '\\PeertestSingleuniquekey';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Key' => $i + 10,
                    'Value' => \rand(),
                ),
                'retrieveMethod' => 'retrieveByKey'
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_SingleUniqueKey
     */
    public function testSingleUniqueKey($ClassName, $Data, $retrieveMethod) {
        $PeerClass = "{$ClassName}Peer";
        $ObjectClass = $ClassName;
        $this->assertTrue(method_exists($PeerClass, $retrieveMethod));
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $PeerClass::$retrieveMethod($Data['Key']);
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $PeerClass::$retrieveMethod($Data['Key']);
        $this->assertTrue($Object instanceof MockContainer);
    }

    public function DataProvider_MultipleUniqueKey() {
        $this->simpleBuild('multipleuniquekey', "Peertest");
        $ClassName = '\\PeertestMultipleuniquekey';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Key1' => $i + 10,
                    'Key2' => $i + 20,
                    'Value' => \rand(),
                ),
                'retrieveMethod' => 'retrieveByKey1Key2'
            );
        }
        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_MultipleUniqueKey
     */
    public function testMultipleUniqueKey($ClassName, $Data, $retrieveMethod) {
        $PeerClass = "{$ClassName}Peer";
        $ObjectClass = $ClassName;
        $this->assertTrue(method_exists($PeerClass, $retrieveMethod));
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $PeerClass::$retrieveMethod($Data['Key1'], $Data['Key2']);
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $PeerClass::$retrieveMethod($Data['Key1'], $Data['Key2']);
        $this->assertTrue($Object instanceof MockContainer);
    }

}