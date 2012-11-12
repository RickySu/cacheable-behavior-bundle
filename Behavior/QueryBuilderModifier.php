<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use RickySu\CacheableBehaviorBundle\ContainerHolder\Holder;
use RickySu\CacheableBehaviorBundle\Renderer\Renderer;
use \PropelPHPParser;

class QueryBuilderModifier {

    protected $behavior;
    protected $table;
    protected $queryBuilder;    
             
    public function __construct($behavior) {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();        
    }

    public function queryMethods($builder) {
        $this->queryBuilder = $builder;
        $Script = '';
        $Script.=$this->addGetTagcache();
        $UniqueIndexs=array();
        $Script.=$this->addUniqueIndexCache($UniqueIndexs);
        $Script.=$this->addFindHook($UniqueIndexs);
        return $Script;
    }

    protected function addFindHook($UniqueIndexs){        
        return $this->behavior->renderTemplate('findHooklQuery.php',array('UniqueIndexs'=>$UniqueIndexs));
    }
    
    public function queryFilter(&$script) {
        $this->addPKCache($script);
    }

    protected function replaceMethodName($script, $MethodName) {
        $Pattern = '/public\s+function\s+(' . $MethodName . ')/i';
        return preg_replace($Pattern, "protected function rebuild_$1", $script);
    }

    protected function replaceFindPKMultiplePK($PKs) {                
        return $this->behavior->renderTemplate('findPKMultiplePK.php', array(
                    'PKs' => $PKs,
                    'queryBuilder' => $this->queryBuilder,
                    'peerBuilder' => $this->queryBuilder->getPeerBuilder(),
                ));
    }

    /**
     *
     * @param Column $PK
     */
    protected function replaceFindPKSinglePK($PK) {        
        return $this->behavior->renderTemplate('findPKSinglePK.php', array(
                    'PK' => $PK,
                    'queryBuilder' => $this->queryBuilder,
                    'peerBuilder' => $this->queryBuilder->getPeerBuilder(),
                ));
    }

    protected function addPKCache(&$script) {
        if ($this->behavior->getParameter('primarykey_cache') != 'true') {
            return;
        }
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod('findPk');
        $OldMethod = $this->replaceMethodName($OldMethod,'findPk');
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

    protected function addUniqueIndexCache(&$UniqueIndexs) {
        if ($this->behavior->getParameter('uniqueindex_cache') != 'true') {
            return;
        }
        $Script = '';
        foreach ($this->table->getUnices() as $Unique) {
            $Columns = array();
            foreach ($Unique->getColumns() as $Col) {
                $Columns[] = $this->table->getColumn($Col);
            }            
            array_push($UniqueIndexs,$Columns);
            $Script.=$this->behavior->renderTemplate('UniqueIndexCacheQuery', array(
                'Columns' => $Columns,
                'ObjectClassName' => $this->queryBuilder->getObjectClassname(),
                'queryBuilder' => $this->queryBuilder,
                'peerBuilder' => $this->queryBuilder->getPeerBuilder(),
                    )
            );            
        }        
        return $Script;
    }

    protected function addGetTagcache() {
        return $this->behavior->renderTemplate('getTagcacheMethod.php',array('static'=>false));
    }

}