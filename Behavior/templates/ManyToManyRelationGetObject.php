

    /**
     * Gets a collection of <?php echo $relatedObjectClassName?> objects cache tag related by a many-to-many relationship
     * to the current object by way of the <?php echo $crossRefTableName?> cross-reference table.
     *
     * @return string
     */     
    public function get<?php echo $relCol?>CacheTag()
    {        
        return <?php echo $CacheTag?>;
    }
    
    /**
     * Gets a collection of <?php echo $relatedObjectClassName?> objects related by a many-to-many relationship
     * to the current object by way of the <?php echo $crossRefTableName?> cross-reference table.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this <?php echo $ObjectClassName?> is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria Optional query object to filter the query
     * @param PropelPDO $con Optional connection object
     *
     * @return PropelObjectCollection|<?php echo $relatedObjectClassName?>[] List of <?php echo $relatedObjectClassName?> objects
     */     
    public function get<?php echo $relCol?>($criteria = null, PropelPDO $con = null)
    {         
         if($criteria===null && $this-><?php echo $collName?>){
             return $this-><?php echo $collName?>;
         }
         if($this->isNew()){
             return $this->rebuild_get<?php echo $relCol?>($criteria,$con);
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
         $TagCacheTags[]=$this->get<?php echo $relCol?>CacheTag();
         $CacheKey=<?php echo $CacheKey?>;
         $Cache=$this->getTagcache();         
         if($Objects=$Cache->get($CacheKey)){         
             if($criteria!==null){
                 $this-><?php echo $collName?> = $Objects;
             }
             return $Objects;
         }
         if($Objects=$this->rebuild_get<?php echo $relCol?>($criteria,$con)){
             $Cache->set($CacheKey,$Objects,$TagCacheTags);
         }         
         return $Objects;
         
    }
