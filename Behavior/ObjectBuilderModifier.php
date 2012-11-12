<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use RickySu\CacheableBehaviorBundle\ContainerHolder\Holder;
use RickySu\CacheableBehaviorBundle\Renderer\Renderer;
use \PropelPHPParser;

class ObjectBuilderModifier {

    protected $behavior;
    protected $table;
    protected $objectBuilder;

    public function __construct($behavior) {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
    }

    public function objectMethods($builder) {
        $this->objectBuilder = $builder;
        $Script = '';
        $Script.=$this->addGetTagcache();
        return $Script;
    }

    protected function generateCacheKey($Keys) {
        return $this->behavior->renderTemplate('UniqueKey.php', array(
                    'ObjectClassName' => $this->objectBuilder->getPeerBuilder()->getObjectClassname(),
                    'Keys' => $Keys,
                    'use_property'=>true,
                ));
    }

    protected function addClearUniqueCache() {
        $Uniques = array();
        if ($this->behavior->getParameter('primarykey_cache') == 'true') {
            $PKs = $this->table->getPrimaryKey();
            $Uniques[] = $PKs;
        }
        if ($this->behavior->getParameter('uniqueindex_cache') == 'true') {
            foreach ($this->table->getUnices() as $Unique) {
                $Keys = array();
                foreach ($Unique->getColumns() as $Col) {
                    $Keys[] = $this->table->getColumn($Col);
                }
                $Uniques[] = $Keys;
            }
        }        
        $CacheKeys=array();
        foreach ($Uniques as $UniqueKeyPair) {
            $CacheKeys[]= $this->generateCacheKey($UniqueKeyPair);
        }
        return $this->behavior->renderTemplate('clearUniqueIndexCacheObject',array('CacheKeys'=>$CacheKeys));
    }

    public function postSave($builder) {
        $this->objectBuilder = $builder;
        return $this->addClearUniqueCache();
    }
    public function postDelete($builder) {
        $this->objectBuilder = $builder;
        return $this->addClearUniqueCache();
    }

    public function objectFilter(&$script) {
        return;
        $this->addPKCache($script);
    }

    protected function replaceMethodName($script, $MethodName) {
        $Pattern = '/public\s+function\s+(' . $MethodName . ')/i';
        return preg_replace($Pattern, "protected function rebuild_$1", $script);
    }

    protected function addPKCache(&$script) {
        if ($this->behavior->getParameter('primarykey_cache') != 'true') {
            return;
        }
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod('findPk');
        $OldMethod = $this->replaceMethodName($OldMethod, 'findPk');
        $PKs = $this->table->getPrimaryKey();
        if (!$this->table->hasPrimaryKey()) {
            return;
        }
        if (count($PKs) == 1) {
            $ReplaceScript = $this->replaceFindPKSinglePK($PKs[0]);
        } else {
            $ReplaceScript = $this->replaceFindPKMultiplePK($PKs);
        }
        $parser->replaceMethod('findPk', $ReplaceScript . $OldMethod);
        $script = $parser->getCode();
    }

    protected function addGetTagcache() {
        return $this->behavior->renderTemplate('getTagcacheMethod.php', array('static' => false));
    }

}