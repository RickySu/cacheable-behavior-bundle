<?php

namespace RickySu\CacheableBehaviorBundle\Tests;

use RickySu\CacheableBehaviorBundle\Tests\Base;
use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;

class QueryBuilderModifierTest extends Base
{
    public function setup()
    {
        $this->prepareMockTagcache();
    }

    public function DataProvider_SinglePK()
    {
        $this->simpleBuild('singleprimarykey', "QueryTest");
        $ClassName = '\\QueryTestSinglepk';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Value' => \rand(),
                ),
            );
        }

        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_SinglePK
     */
    public function testSinglePK($ClassName, $Data)
    {
        $QueryClass = "{$ClassName}Query";
        $ObjectClass = $ClassName;
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $QueryClass::create()->findPk($Data['Id']);
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $QueryClass::create()->findPk($Data['Id']);
        $this->assertTrue($Object instanceof MockContainer);
    }

    public function DataProvider_MultiplePK()
    {
        $this->simpleBuild('multipleprimarykey', "QueryTest");
        $ClassName = '\\QueryTestMultiplepk';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id1' => $i,
                    'Id2' => $i + 10,
                    'Value' => \rand(),
                ),
            );
        }

        return $Row;
    }

    /**
     *
     * @dataProvider DataProvider_MultiplePK
     */
    public function testMultiplePK($ClassName, $Data)
    {
        $QueryClass = "{$ClassName}Query";
        $ObjectClass = $ClassName;
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $QueryClass::create()->findPk(array($Data['Id1'], $Data['Id2']));
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $QueryClass::create()->findPk(array($Data['Id1'], $Data['Id2']));
        $this->assertTrue($Object instanceof MockContainer);
    }

    public function DataProvider_SingleUniqueKey()
    {
        $this->simpleBuild('singleuniquekey', "QueryTest");
        $ClassName = '\\QueryTestSingleuniquekey';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
                    'Id' => $i,
                    'Key' => $i + 10,
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
    public function testSingleUniqueKey($ClassName, $Data)
    {
        $QueryClass = "{$ClassName}Query";
        $ObjectClass = $ClassName;
        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $QueryClass::create()->findOneByKey($Data['Key']);
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $QueryClass::create()->findOneByKey($Data['Key']);
        $this->assertTrue($Object instanceof MockContainer);
        $Object = $QueryClass::create()->filterByKey($Data['Key'])->findOne();
        $this->assertTrue($Object instanceof MockContainer);
        $Objects = $QueryClass::create()->filterByKey($Data['Key'])->find();
        $this->assertTrue($Objects[0] instanceof MockContainer);
    }

    public function DataProvider_MultipleUniqueKey()
    {
        $this->simpleBuild('multipleuniquekey', "QueryTest");
        $ClassName = '\\QueryTestMultipleuniquekey';
        for ($i = 0; $i < 5; $i++) {
            $Row[] = array(
                'ClassName' => $ClassName,
                'OriginData' => array(
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
    public function testMultipleUniqueKey($ClassName, $Data)
    {
        $QueryClass = "{$ClassName}Query";
        $ObjectClass = $ClassName;

        $Object = new $ObjectClass();
        $Object->fromArray($Data);
        $Object->save();
        $Object = $QueryClass::create()->filterByKey1($Data['Key1'])->filterByKey2($Data['Key2'])->findOne();
        $this->assertTrue($Object instanceof $ObjectClass);
        $Object = $QueryClass::create()->filterByKey1($Data['Key1'])->filterByKey2($Data['Key2'])->findOne();
        $this->assertTrue($Object instanceof MockContainer);
        $Object = $QueryClass::create()->filterByKey2($Data['Key2'])->filterByKey1($Data['Key1'])->findOne();
        $Object = $QueryClass::create()->filterByKey2($Data['Key2'])->filterByKey1($Data['Key1'])->filterById($Data['Id'])->findOne();
        $this->assertFalse($Object instanceof MockContainer);
        $Objects = $QueryClass::create()->filterByKey2($Data['Key2'])->filterByKey1($Data['Key1'])->find();
        $this->assertTrue($Objects[0] instanceof MockContainer);
    }

}
