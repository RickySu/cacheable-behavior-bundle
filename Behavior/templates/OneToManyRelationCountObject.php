

    /**
     * Returns the number of related <?php echo $className?> objects cache tag.
     *
     * @return string
     */
    public function count<?php echo $relCol?>CacheTag()
    {
        return <?php echo $CacheTag?>;
    }

    /**
     * Returns the number of related <?php echo $className?> objects.
     *
     * @param  Criteria        $criteria
     * @param  boolean         $distinct
     * @param  PropelPDO       $con
     * @return int             Count of related <?php echo $className?> objects.
     * @throws PropelException
     */
    public function count<?php echo $relCol?>($criteria = null, $distinct = false, PropelPDO $con = null)
    {
         if ($this->isNew()) {
             return $this->rebuild_count<?php echo $relCol?>($criteria,$distinct,$con);
         }
         $CriteriaHash='';
         $TagCacheTags=array();
         if ($criteria) {
            $Map=$criteria->getMap();
            ksort($Map);
            foreach ($Map as $Key => $Val) {
                 $criteria->remove($Key);
                 $criteria->add($Val);
            }
            $CriteriaHash='#'.md5($criteria->toString());
         }
         $TagCacheTags[]=$this->count<?php echo $relCol?>CacheTag();
         $CacheKey=<?php echo $CacheKey?>;
         $Cache=$this->getTagcache();
         if ($Counts=$Cache->get($CacheKey)) {
             return $Counts;
         }
         if ($Counts=$this->rebuild_count<?php echo $relCol?>($criteria,$distinct,$con)) {
             $Cache->set($CacheKey,$Counts,$TagCacheTags);
         }

         return $Counts;
    }
