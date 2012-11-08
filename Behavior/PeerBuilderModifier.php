<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use RickySu\CacheableBehaviorBundle\ContainerHolder\Holder;
use RickySu\CacheableBehaviorBundle\Renderer\Renderer;
use \PropelPHPParser;

class PeerBuilderModifier {

    protected $behavior;
    protected $table;
    protected $peerBuilder;
    protected $twig;

    public function __construct($behavior) {
        $this->behavior = $behavior;
        $this->table = $behavior->getTable();
        $this->twig = Renderer::getInstance();
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

    protected function replaceRetrieveByPKMultiplePK($PKs, $OldMethod) {
        $table = $this->table;
        $OldMethod = $this->replaceMethodName($OldMethod, $this->peerBuilder->getRetrieveMethodName());
        return $this->behavior->renderTemplate('retrieveByPKMultiplePK.php', array(
                    'PKs' => $PKs,
                    'peerBuilder' => $this->peerBuilder,
                )) . $OldMethod;
    }

    /**
     *
     * @param Column $PK
     */
    protected function replaceRetrieveByPKSinglePK($PK, $OldMethod) {
        $OldMethod = $this->replaceMethodName($OldMethod, $this->peerBuilder->getRetrieveMethodName());
        return $this->behavior->renderTemplate('retrieveByPKSinglePK.php', array(
                    'PK' => $PK,
                    'peerBuilder' => $this->peerBuilder,
                )) . $OldMethod;
    }

    protected function addPKCache(&$script) {
        if ($this->behavior->getParameter('primarykey_cache') != 'true') {
            return;
        }
        $parser = new PropelPHPParser($script, true);
        $OldMethod = $parser->findMethod($this->peerBuilder->getRetrieveMethodName());
        $PKs = $this->table->getPrimaryKey();
        if (!$this->table->hasPrimaryKey()) {
            return;
        }
        if (count($PKs) == 1) {
            $ReplaceScript = $this->replaceRetrieveByPKSinglePK($PKs[0], $OldMethod);
        } else {
            $ReplaceScript = $this->replaceRetrieveByPKMultiplePK($PKs, $OldMethod);
        }
        $parser->replaceMethod($this->peerBuilder->getRetrieveMethodName(), $ReplaceScript);
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
                'ObjectClassName' => $this->peerBuilder->getObjectClassname(),
                'peerBuilder' => $this->peerBuilder,
                    )
            );
        }
        return $Script;
    }

    protected function addGetTagcache() {
        $this->peerBuilder->declareClassNamespace('EventProxy', 'RickySu\\CacheableBehaviorBundle\\Event');
        $this->peerBuilder->declareClassNamespace('GetTagcacheEvent', 'RickySu\\CacheableBehaviorBundle\\Event');
        return $this->behavior->renderTemplate('getTagcachePeer.php');
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