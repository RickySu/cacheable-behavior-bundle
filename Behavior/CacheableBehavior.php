<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use \Behavior;
use RickySu\CacheableBehaviorBundle\Behavior\PeerBuilderModifier;
use RickySu\CacheableBehaviorBundle\Behavior\QueryBuilderModifier;

class CacheableBehavior extends Behavior {

    protected $peerBuilderModifier=null;
    protected $queryBuilderModifier=null;
    
    protected function setDefaultParam()
    {
    }

    public function getPeerBuilderModifier() {
        if (is_null($this->peerBuilderModifier)){
            $this->peerBuilderModifier=new PeerBuilderModifier($this);
        }
        return $this->peerBuilderModifier;
    }

    public function getQueryBuilderModifier() {
        if (is_null($this->queryBuilderModifier)){
            $this->queryBuilderModifier=new QueryBuilderModifier($this);
        }
        return $this->queryBuilderModifier;
    }
    
}