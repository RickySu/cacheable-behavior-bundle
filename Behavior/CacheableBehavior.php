<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use \Behavior;
use RickySu\CacheableBehaviorBundle\Behavior\PeerBuilderModifier;
use RickySu\CacheableBehaviorBundle\Behavior\QueryBuilderModifier;
use RickySu\CacheableBehaviorBundle\Behavior\ObjectBuilderModifier;

class CacheableBehavior extends Behavior {

    protected $peerBuilderModifier=null;
    protected $queryBuilderModifier=null;
    protected $objectBuilderModifier=null;
    
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
    
    public function getObjectBuilderModifier() {
        if (is_null($this->objectBuilderModifier)){
            $this->objectBuilderModifier=new ObjectBuilderModifier($this);
        }
        return $this->objectBuilderModifier;
    }
    
}