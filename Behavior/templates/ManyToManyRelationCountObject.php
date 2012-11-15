

    /**
     * Gets the number of <?php echo $relatedObjectClassName?> objects cache tag related by a many-to-many relationship
     * to the current object by way of the <?php echo $crossRefTableName?> cross-reference table.
     *
     * @return string
     */
    public function count<?php echo $relCol?>CacheTag()
    {
        return <?php echo $CacheTags?>;
    }
    
    /**
     * Gets the number of <?php echo $relatedObjectClassName?> objects related by a many-to-many relationship
     * to the current object by way of the <?php echo $crossRefTableName?> cross-reference table.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param boolean $distinct Set to true to force count distinct
     * @param PropelPDO $con Optional connection object
     *
     * @return int the number of related <?php echo $relatedObjectClassName?> objects
     */
    public function count<?php echo $relCol?>($criteria = null, $distinct = false, PropelPDO $con = null)
    {    
         if($this->isNew()){
             return $this->rebuild_count<?php echo $relCol?>($criteria,$distinct,$con);
         }         
         $CriteriaHash='';
         $TagCacheTags=array();
         if($criteria){
            $Map=$criteria->getMap();
            ksort($Map);
            foreach($Map as $Key => $Val){
                 $criteria->remove($Key);
                 $criteria->add($Val);
            }
            $CriteriaHash='#'.md5($criteria->toString());            
         }
         $TagCacheTags[]=$this->count<?php echo $relCol?>CacheTag();
         $CacheKey=<?php echo $CacheKey?>;
         $Cache=$this->getTagcache();
         if($Counts=$Cache->get($CacheKey)){
             return $Counts;
         }
         if($Counts=$this->rebuild_count<?php echo $relCol?>($criteria,$distinct,$con)){
             $Cache->set($CacheKey,$Counts,$TagCacheTags);
         }         
         return $Counts;
    }
