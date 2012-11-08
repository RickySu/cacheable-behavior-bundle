

    /**
     * Retrieve a single object by pkey.
     *
     * @param      <?php echo $PK->getPhpType()?> $pk the primary key.
     * @param      PropelPDO $con the connection to use
     * @return     <?php echo $peerBuilder->getObjectClassname()?> 
     */
    public static function <?php echo $peerBuilder->getRetrieveMethodName()?>($pk, PropelPDO $con = null)
    {
        $CacheKey="Model:<?php echo $peerBuilder->getObjectClassname()?>:$pk";
        $Cache=self::getTagcache();
        if($Obj=$Cache->get($CacheKey)){
            return $Obj;
        }
        if($Obj=self::rebuild_<?php echo $peerBuilder->getRetrieveMethodName()?>($pk,$con)){
            $Cache->set($CacheKey,$Obj);
        }
        return $Obj;
    }
