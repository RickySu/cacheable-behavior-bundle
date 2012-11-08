<?php

namespace RickySu\CacheableBehaviorBundle\Behavior;

use \Behavior;
use RickySu\CacheableBehaviorBundle\Behavior\PeerBuilderModifier;

class CacheableBehavior extends Behavior {

    protected $peerBuilderModifier=null;

    protected function setDefaultParam()
    {
    }

    public function getPeerBuilderModifier() {
        if (is_null($this->peerBuilderModifier)){
            $this->peerBuilderModifier=new PeerBuilderModifier($this);
        }
        return $this->peerBuilderModifier;
    }

}