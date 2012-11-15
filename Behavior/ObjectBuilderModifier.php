<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use RickySu\CacheableBehaviorBundle\ContainerHolder\Holder;
use RickySu\CacheableBehaviorBundle\Renderer\Renderer;
use \PropelPHPParser;
use \ForeignKey;

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
                    'ObjectClassName' => $this->objectBuilder->getPeerBuilder()->getObjectClassName(),
                    'Keys' => $Keys,
                    'use_property' => true,
                ));
    }

    protected function addManyToManyCache(&$script) {
        if ($this->behavior->getParameter('relation_cache') == 'false') {
            return;
        }
        foreach ($this->table->getCrossFks() as $fkList) {
            list($refFK, $crossFK) = $fkList;
            $this->generateManyToManyCacheScript($script, $refFK, $crossFK,'Get');
        }
    }

    protected function generateManyToManyCacheScript(&$script, ForeignKey $refFK, ForeignKey $crossFK,$Method='Get') {
        $relCol = $this->objectBuilder->getFKPhpNameAffix($crossFK, true);
        $relatedObjectClassName = $this->objectBuilder->getNewStubObjectBuilder($crossFK->getForeignTable())->getClassname();
        $selfRelationName = $this->objectBuilder->getFKPhpNameAffix($refFK, $plural = false);
        $relatedQueryClassName = $this->objectBuilder->getNewStubQueryBuilder($crossFK->getForeignTable())->getClassname();
        $crossRefTableName = $crossFK->getTableName();
        $collName = 'coll' . $this->objectBuilder->getFKPhpNameAffix($crossFK, true);
        $CacheTags = $this->behavior->renderTemplate("ManyToManyRelationCachetags{$Method}Object", array(
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'relCol' => $relCol,
            'Keys' => $this->table->getPrimaryKey(),
                ));
        $CacheKey = $this->behavior->renderTemplate("ManyToManyRelationCacheKey{$Method}Object", array(
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'relCol' => $relCol,
            'Keys' => $this->table->getPrimaryKey(),
                ));
        $ReplaceScript = $this->behavior->renderTemplate("ManyToManyRelation{$Method}Object", array(
            'relCol' => $relCol,
            'relatedObjectClassName' => $relatedObjectClassName,
            'selfRelationName' => $selfRelationName,
            'crossRefTableName' => $crossRefTableName,
            'collName' => $collName,
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'CacheKey' => $CacheKey,
            'CacheTags' => $CacheTags,
                ));
        $MethodName=strtolower($Method).$relCol;
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod($MethodName);
        $OldMethod = $this->replaceMethodName($OldMethod, $MethodName);
        $parser->replaceMethod($MethodName, $ReplaceScript . $OldMethod);
        $script = $parser->getCode();
    }

    protected function addOneToManyCache(&$script) {
        if ($this->behavior->getParameter('relation_cache') == 'false') {
            return;
        }
        foreach ($this->table->getReferrers() as $refFK) {
            $UniqueIndexs = array();
            if ($refFK->isLocalPrimaryKey()) {
                //skip one to one relation
                continue;
            }
            $LocalColumn = array();
            foreach ($refFK->getLocalColumns() as $Col) {
                $LocalColumn[] = $Col;
            }
            foreach ($refFK->getTable()->getUnices() as $Unique) {
                $Columns = array();
                foreach ($Unique->getColumns() as $Col) {
                    $Columns[] = $Col;
                }
                $UniqueIndexs[] = $Columns;
            }
            $isOnetoOneRelation = false;
            foreach ($UniqueIndexs as $UniqueIndex) {
                if (count($UniqueIndex) && !array_diff($UniqueIndex, $LocalColumn)) {
                    //is one to one relation skip
                    $this->generateOneToOneGetCacheScript($script, $refFK);
                    $isOnetoOneRelation = true;
                    break;
                }
            }
            if ($isOnetoOneRelation) {
                continue;
            }
            $this->generateOneToManyCacheScript($script, $refFK,'Get');
            $this->generateOneToManyCacheScript($script, $refFK,'Count');
        }
    }   

    protected function generateOneToManyCacheScript(&$script, ForeignKey $refFK,$Method='Get') {
        $joinedTableObjectBuilder = $this->objectBuilder->getNewObjectBuilder($refFK->getTable());
        $className = $joinedTableObjectBuilder->getObjectClassName();
        $relCol = $this->objectBuilder->getRefFKPhpNameAffix($refFK, true);
        $collName = $this->objectBuilder->getRefFKCollVarName($refFK);
        $CacheTags = $this->behavior->renderTemplate("OneToManyRelationCachetags{$Method}Object", array(
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'relCol' => $relCol,
            'Keys' => $this->table->getPrimaryKey(),
                ));
        $CacheKey = $this->behavior->renderTemplate("OneToManyRelationCacheKey{$Method}Object", array(
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'relCol' => $relCol,
            'Keys' => $this->table->getPrimaryKey(),
                ));
        $ReplaceScript = $this->behavior->renderTemplate("OneToManyRelation{$Method}Object", array(
            'CacheKey' => $CacheKey,
            'CacheTags' => $CacheTags,
            'className' => $className,
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'collName' => $collName,
            'relCol' => $relCol,
                ));
        $MethodName=strtolower($Method).$relCol;
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod($MethodName);
        $OldMethod = $this->replaceMethodName($OldMethod, $MethodName);
        $parser->replaceMethod($MethodName, $ReplaceScript . $OldMethod);
        $script = $parser->getCode();
    }

    protected function addClearManyToManyCache($ClearGetCache = true, $ClearCountCache = true) {
        if ($this->behavior->getParameter('relation_cache') == 'false') {
            return;
        }
        foreach ($this->table->getCrossFks() as $fkList) {
            list($refFK, $crossFK) = $fkList;
            $Script = '';
            if ($ClearGetCache) {
                $Script.=$this->generateClearManyToManyCacheGetScript($refFK, $crossFK);
            }
            if ($ClearCountCache) {
                $Script.=$this->generateClearManyToManyCacheCountScript($refFK, $crossFK);
            }
            return $Script;
        }
        return '';
    }

    protected function addClearOneToManyCache($ClearGetCache = true, $ClearCountCache = true) {
        foreach ($this->table->getForeignKeys() as $refFK) {
            $UniqueIndexs = array();
            if ($refFK->isLocalPrimaryKey()) {
                //skip one to one relation
                continue;
            }
            $LocalColumn = array();
            foreach ($refFK->getLocalColumns() as $Col) {
                $LocalColumn[] = $Col;
            }
            foreach ($refFK->getTable()->getUnices() as $Unique) {
                $Columns = array();
                foreach ($Unique->getColumns() as $Col) {
                    $Columns[] = $Col;
                }
                $UniqueIndexs[] = $Columns;
            }

            $isOnetoOneRelation = false;
            foreach ($UniqueIndexs as $UniqueIndex) {
                if (count($UniqueIndex) && !array_diff($UniqueIndex, $LocalColumn)) {
                    //is one to one relation skip
                    $isOnetoOneRelation = true;
                    break;
                }
            }

            if ($isOnetoOneRelation) {
                continue;
            }
            $Script = '';
            if ($ClearGetCache) {
                $Script.=$this->generateClearOneToManyCacheScript($refFK,'Get');
            }
            if ($ClearCountCache) {
                $Script.=$this->generateClearOneToManyCacheScript($refFK,'Count');
            }
            return $Script;
        }
        return '';
    }


    protected function generateOneToOneGetCacheScript(&$script, ForeignKey $refFK) {
        if ($this->behavior->getParameter('uniqueindex_relation') == 'false') {
            return;
        }
        $joinedTableObjectBuilder = $this->objectBuilder->getNewObjectBuilder($refFK->getTable());
        $className = $joinedTableObjectBuilder->getObjectClassName();
        $relCol = $this->objectBuilder->getRefFKPhpNameAffix($refFK, false);
        $relCols = $this->objectBuilder->getRefFKPhpNameAffix($refFK, true);
        $ReplaceScript = $this->behavior->renderTemplate('OneToOneRelationObject', array(
            'className' => $className,
            'relCol' => $relCol,
            'relCols' => $relCols,
                ));
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod("get$relCols");
        $OldMethod = $this->replaceMethodName($OldMethod, "get$relCols");
        $parser->replaceMethod("get$relCols", $ReplaceScript . $OldMethod);
        $script = $parser->getCode();
    }
    
    protected function generateClearOneToManyCacheScript(ForeignKey $refFK,$Method='Get') {
        $Keys = array();
        foreach ($refFK->getTable()->getPrimaryKey() as $Column) {
            $Keys[] = $Column;
        }
        $relCol = $this->objectBuilder->getRefFKPhpNameAffix($refFK, true);
        $CacheTags = $this->behavior->renderTemplate("OneToManyRelationCachetags{$Method}Object", array(
            'ObjectClassName' => $refFK->getForeignTable()->getPhpName(),
            'relCol' => $relCol,
            'Object' => $refFK->getForeignTable()->getPhpName(),
            'Keys' => $Keys,
                ));
        return $this->behavior->renderTemplate("clearOneToManyCache{$Method}Object", array('CacheTags' => $CacheTags));
    }

    protected function addClearUniqueCache() {
        $Uniques = array();
        if ($this->behavior->getParameter('primarykey_cache') != 'false') {
            $PKs = $this->table->getPrimaryKey();
            $Uniques[] = $PKs;
        }
        if ($this->behavior->getParameter('uniqueindex_cache') != 'false') {
            foreach ($this->table->getUnices() as $Unique) {
                $Keys = array();
                foreach ($Unique->getColumns() as $Col) {
                    $Keys[] = $this->table->getColumn($Col);
                }
                $Uniques[] = $Keys;
            }
        }
        $CacheKeys = array();
        foreach ($Uniques as $UniqueKeyPair) {
            $CacheKeys[] = $this->generateCacheKey($UniqueKeyPair);
        }
        return $this->behavior->renderTemplate('clearUniqueIndexCacheObject', array('CacheKeys' => $CacheKeys));
    }

    public function postSave($builder) {
        $this->objectBuilder = $builder;
        $script = '';
        $script.=$this->addClearUniqueCache();
        $script.=$this->addClearOneToManyCache(true, false);
        if ($script !== '') {
            $script = $this->behavior->renderTemplate('getTagcacheforClearCacheObject') . $script;
        }
        return $script;
    }

    public function postInsert($builder) {
        $this->objectBuilder = $builder;
        $script = '';
        $script.=$this->addClearOneToManyCache(false, true);
        if ($script !== '') {
            $script = $this->behavior->renderTemplate('getTagcacheforClearCacheObject') . $script;
        }
        return $script;
    }

    public function preDelete($builder) {
        $this->objectBuilder = $builder;
        $script = '';
        $script.=$this->addClearUniqueCache();
        $script.=$this->addClearOneToManyCache(true, true);
        if ($script !== '') {
            $script = $this->behavior->renderTemplate('getTagcacheforClearCacheObject') . $script;
        }
        return $script;
    }

    public function objectFilter(&$script) {
        //skip one to one relation cache. use primary key or unique index query instead
        $this->addOneToManyCache($script);
        $this->addManyToManyCache($script);
    }

    protected function replaceMethodName($script, $MethodName) {
        $Pattern = '/public\s+function\s+(' . $MethodName . ')/i';
        return preg_replace($Pattern, "protected function rebuild_$1", $script);
    }

    protected function addGetTagcache() {
        return $this->behavior->renderTemplate('getTagcacheMethod.php', array('static' => false));
    }

}