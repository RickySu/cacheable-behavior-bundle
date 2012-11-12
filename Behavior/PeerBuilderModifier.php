<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use RickySu\CacheableBehaviorBundle\ContainerHolder\Holder;
use RickySu\CacheableBehaviorBundle\Renderer\Renderer;
use \PropelPHPParser;

class PeerBuilderModifier {

    protected $behavior;
    protected $table;
    protected $peerBuilder;    

    public function __construct($behavior) {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();        
    }

    public function staticMethods($builder) {
        $this->peerBuilder = $builder;
        $Script = '';
        $Script.=$this->addUniqueIndexCache();
        $Script.=$this->addGetTagcache();
        return $Script;
    }

    public function peerFilter(&$script) {
        $this->addPKCache($script);
    }

    protected function replaceMethodName($script, $MethodName) {
        $Pattern = '/public\s+static\s+function\s+(' . $MethodName . ')/i';
        return preg_replace($Pattern, "protected static function rebuild_$1", $script);
    }

    protected function generateCacheKey($Keys){
        return $this->behavior->renderTemplate('UniqueKey.php',array(
                                'ObjectClassName'=>$this->peerBuilder->getObjectClassname(),
                                'Keys'=>$Keys,
                            ));
    }
    
    protected function replaceRetrieveByPK($PKs) {                
        return $this->behavior->renderTemplate('retrieveByPK.php', array(
                    'PKs' => $PKs,
                    'CacheKey'=>$this->generateCacheKey($PKs),
                    'peerBuilder' => $this->peerBuilder,
                ));
    }

    protected function addPKCache(&$script) {
        if ($this->behavior->getParameter('primarykey_cache') != 'true') {
            return;
        }
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod($this->peerBuilder->getRetrieveMethodName());
        $OldMethod = $this->replaceMethodName($OldMethod, $this->peerBuilder->getRetrieveMethodName());
        $PKs = $this->table->getPrimaryKey();
        if (!$this->table->hasPrimaryKey()) {
            return;
        }
        $ReplaceScript = $this->replaceRetrieveByPK($PKs);
        $parser->replaceMethod($this->peerBuilder->getRetrieveMethodName(), $ReplaceScript.$OldMethod);
        $script = $parser->getCode();
    }

    protected function addUniqueIndexCache() {
        $TemplateName = 'UniqueIndexCachePeer.php';
        if ($this->behavior->getParameter('uniqueindex_cache') != 'true') {
            $TemplateName = 'UniqueIndexPeer.php';
        }
        $Script = '';
        foreach ($this->table->getUnices() as $Unique) {
            $Columns = array();
            foreach ($Unique->getColumns() as $Col) {
                $Columns[] = $this->table->getColumn($Col);
            }
            $Script.=$this->behavior->renderTemplate($TemplateName, array(
                'Columns' => $Columns,                
                'CacheKey'=>$this->generateCacheKey($Columns),
                'peerBuilder' => $this->peerBuilder,
                    )
            );
        }
        return $Script;
    }

    protected function addGetTagcache() {
        return $this->behavior->renderTemplate('getTagcacheMethod.php',array('static'=>true));
    }

    protected function getTagsScript() {

        $TagScript = '';

        if ($this->behavior->getTable()->hasBehavior('nested_set') && $this->behavior->getParameter('nested_set_cache')) {
            $TagScript .= "\"Tag:{$this->PeerBuilder->getObjectClassname()}#Nestedset\"";
        }

        if ($TagScript == '') {
            return '';
        }
        return ",array($TagScript)";
    }

}