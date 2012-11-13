/**
 * Gets an array of <?php echo $className?> objects which contain a foreign key that references this object.
 *
 * If the $criteria is not null, it is used to always fetch the results from the database.
 * Otherwise the results are fetched from the database the first time, then cached.
 * Next time the same method is called without $criteria, the cached collection is returned.
 * If this <?php echo $ObjectClassname?> is new, it will return
 * an empty collection or the current collection; the criteria is ignored on a new object.
 *
 * @param Criteria $criteria optional Criteria object to narrow the query
 * @param PropelPDO $con optional connection object
 * @return PropelObjectCollection|<?php echo $className?>[] List of <?php echo $className?> objects
 * @throws PropelException
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
        $TagCacheTags[]=<?php echo $CacheTags?>;
     }
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
