
    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj = $c->findPk(array(<?php foreach($PKs as $PK):?>$<?php echo strtolower($PK->getName())?>, <?php endforeach?>), $con);
     * </code>
     *
     * @param array $key Primary key to use for the query
                         A Primary key composition: [$id, $username]
     * @param PropelPDO $con an optional connection object
     *
     * @return <?php echo $queryBuilder->getObjectClassname()?> |<?php echo $queryBuilder->getObjectClassname()?> []|mixed the result, formatted by the current formatter
     */
     public function findPk($key, PropelPDO $con = null)
     {
<?php foreach($PKs as $Index => $PK):?>
         $<?php echo $PK->getName()?>=$key[<?php echo $Index?>];
<?php endforeach?>
         <?php echo $CacheKey?>
         $Cache=$this->getTagcache();
         if ($Obj=$Cache->get($CacheKey)) {
             return $Obj;
         }
         if ($Obj=$this->rebuild_findPk($key, $con)) {
             $Cache->set($CacheKey,$Obj);
         }

         return $Obj;
     }
