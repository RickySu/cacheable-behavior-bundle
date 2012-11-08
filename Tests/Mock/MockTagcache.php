<?php

namespace RickySu\CacheableBehaviorBundle\Tests\Mock;

use RickySu\CacheableBehaviorBundle\Tests\Mock\MockContainer;
use RickySu\TagcacheBundle\Adapter\Sqlite;

class MockTagcache extends Sqlite {

    protected function InitDBFile() {
        $this->Sqlite = new \PDO('sqlite::memory:');
        $this->insertDBSQL();
    }

    public function set($Key, $var, $Tags = array(), $expire = null) {
        $var = new MockContainer($var);
        return parent::set($Key, $var, $Tags, $expire);
    }

}
