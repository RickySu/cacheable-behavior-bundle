
/**
 * Retrieve a single object by<?php foreach($Columns as $Column):?> <?php echo $Column->getPhpName()?><?php endforeach ?>.
 *
 * @param      PropelPDO $con the connection to use
 * @return     <?php echo $peerBuilder->getObjectClassname()?>
 */
 public static function retrieveBy<?php foreach ($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach ?>(<?php foreach($Columns as $Column):?>$<?php echo $Column->getName()?>, <?php endforeach ?>PropelPDO $con=null) {
     <?php echo $CacheKey?>
     $Cache=self::getTagcache();
     if ($Object=$Cache->get($CacheKey)) {
         return $Object;
     }
     $Criteria=new Criteria();
<?php foreach($Columns as $Column):?>
     $Criteria->add(<?php echo $peerBuilder->getColumnConstant($Column)?>,$<?php echo $Column->getName()?>);
<?php endforeach ?>
     if ($Object=<?php echo $peerBuilder->getPeerClassname()?>::doSelectOne($Criteria,$con)) {
         $Cache->set($CacheKey,$Object);
     }

     return $Object;
 }
