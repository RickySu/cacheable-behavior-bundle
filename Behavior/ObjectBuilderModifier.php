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

    protected function addOneToManyCache(&$script) {
        if ($this->behavior->getParameter('relation_cache') != 'true') {
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
                    $this->generateOneToOneCacheScript($script, $refFK);
                    $isOnetoOneRelation = true;
                    break;
                }
            }
            if ($isOnetoOneRelation) {
                continue;
            }
            $this->generateOneToManyCacheScript($script, $refFK);
        }
    }

    protected function generateOneToOneCacheScript(&$script, ForeignKey $refFK) {
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

    protected function generateOneToManyCacheScript(&$script, ForeignKey $refFK) {
        $joinedTableObjectBuilder = $this->objectBuilder->getNewObjectBuilder($refFK->getTable());
        $className = $joinedTableObjectBuilder->getObjectClassName();
        $relCol = $this->objectBuilder->getRefFKPhpNameAffix($refFK, true);
        $collName = $this->objectBuilder->getRefFKCollVarName($refFK);
        $CacheTags = $this->behavior->renderTemplate('OneToManyRelationCachetagsObject', array(
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'relCol' => $relCol,
            'Keys' => $this->table->getPrimaryKey(),
                ));
        $CacheKey = $this->behavior->renderTemplate('OneToManyRelationCacheKeyObject', array(
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'relCol' => $relCol,
            'Keys' => $this->table->getPrimaryKey(),
                ));
        $ReplaceScript = $this->behavior->renderTemplate('OneToManyRelationObject', array(
            'CacheKey' => $CacheKey,
            'CacheTags' => $CacheTags,
            'className' => $className,
            'ObjectClassName' => $this->objectBuilder->getObjectClassName(),
            'collName' => $collName,
            'relCol' => $relCol,
                ));
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod("get$relCol");
        $OldMethod = $this->replaceMethodName($OldMethod, "get$relCol");
        $parser->replaceMethod("get$relCol", $ReplaceScript . $OldMethod);
        $script = $parser->getCode();
    }

    protected function addClearOneToManyCache() {
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
            return $this->generateClearOneToManyCacheScript($refFK);
        }
        return '';
    }

    protected function generateClearOneToManyCacheScript(ForeignKey $refFK) {
        $Keys = array();
        foreach ($refFK->getForeignColumnObjects() as $Column) {
            $Keys[] = $Column;
        }
        $relCol = $this->objectBuilder->getRefFKPhpNameAffix($refFK, true);
        $CacheTags = $this->behavior->renderTemplate('OneToManyRelationCachetagsObject', array(
            'ObjectClassName' => $refFK->getForeignTable()->getPhpName(),
            'relCol' => $relCol,
            'Object' => $refFK->getForeignTable()->getPhpName(),
            'Keys' => $Keys,
                ));
        return $this->behavior->renderTemplate('clearOneToManyCacheObject', array('CacheTags' => $CacheTags));
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
        $script.=$this->addClearOneToManyCache();
        if ($script !== '') {
            $script = $this->behavior->renderTemplate('getTagcacheforClearCacheObject') . $script;
        }
        return $script;
    }

    public function postDelete($builder) {
        $this->objectBuilder = $builder;
        $script = '';
        $script.=$this->addClearUniqueCache();
        $script.=$this->addClearOneToManyCache();
        if ($script !== '') {
            $script = $this->behavior->renderTemplate('getTagcacheforClearCacheObject') . $script;
        }
        return $script;
    }

    public function objectFilter(&$script) {
        //skip one to one relation cache. use primary key or unique index query instead
        $this->addOneToManyCache($script);
    }

    protected function replaceMethodName($script, $MethodName) {
        $Pattern = '/public\s+function\s+(' . $MethodName . ')/i';
        return preg_replace($Pattern, "protected function rebuild_$1", $script);
    }

    protected function addGetTagcache() {
        return $this->behavior->renderTemplate('getTagcacheMethod.php', array('static' => false));
    }

}