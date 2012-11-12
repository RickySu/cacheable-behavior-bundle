

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param PropelPDO $con an optional connection object
     *
     * @return <?php echo $queryBuilder->getObjectClassname()?>|<?php echo $queryBuilder->getObjectClassname()?>[]|mixed the result, formatted by the current formatter
     */
    public function findPk($pk, PropelPDO $con = null)
    {
        $<?php echo $PK->getName()?>=$pk;
        <?php echo $CacheKey?>        
        $Cache=$this->getTagcache();
        if($Obj=$Cache->get($CacheKey)){
            return $Obj;
        }
        if($Obj=$this->rebuild_findPk($pk,$con)){
            $Cache->set($CacheKey,$Obj);
        }
        return $Obj;
    }
