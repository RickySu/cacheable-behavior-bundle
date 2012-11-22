
/**
 * Retrieve a single object by<?php foreach($Columns as $Column):?> <?php echo $Column->getPhpName()?><?php endforeach ?>.
 *
 * @param      PropelPDO $con the connection to use
 * @return     <?php echo $ObjectClassName?>
 */
 public static function retrieveBy<?php foreach ($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach ?>(<?php foreach($Columns as $Column):?>$<?php echo $Column->getName()?>, <?php endforeach ?>PropelPDO $con=null) {
     $Criteria=new Criteria();
<?php foreach($Columns as $Column):?>
     $Criteria->add(<?php echo $peerBuilder->getColumnConstant($Column)?>,$<?php echo $Column->getName()?>);
<?php endforeach?>

     return <?php echo $peerBuilder->getPeerClassname()?>::doSelectOne($Criteria,$con);
 }
