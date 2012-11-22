

    /**
     * Retrieve object using using composite pkey values.
<?php foreach($PKs as $Index => $Column):?>
     * @param <?php echo $Column->getPhpType()?> <?php echo strtolower($Column->getName())?>
<?php endforeach?>
     * @param  PropelPDO $con
     * @return <?php     echo $peerBuilder->getObjectClassname()?>
     */
     public static function <?php echo $peerBuilder->getRetrieveMethodName()?>(<?php foreach ($PKs as $Column):?>$<?php echo strtolower($Column->getName())?>, <?php endforeach?>PropelPDO $con = null) {
         <?php echo $CacheKey?>
         $Cache=self::getTagcache();
         if ($Obj=$Cache->get($CacheKey)) {
             return $Obj;
         }
         if ($Obj=self::rebuild_<?php echo $peerBuilder->getRetrieveMethodName()?>(<?php foreach($PKs as $Column):?>$<?php echo strtolower($Column->getName())?>, <?php endforeach?>$con)) {
             $Cache->set($CacheKey,$Obj);
         }

         return $Obj;
     }
