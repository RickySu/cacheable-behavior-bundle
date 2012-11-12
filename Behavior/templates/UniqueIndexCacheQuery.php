
/**
 * find a object by<?php foreach($Columns as $Column):?> <?php echo $Column->getPhpName()?><?php endforeach ?>.
 *
 * @param      $name
 * @param      $arguments
 * @return     <?php echo $ObjectClassName?> 
 */
 protected function findOneBy<?php foreach($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>WithCache(&$excuted, PropelPDO $con=null)
 {
     $Criteria=new \Criteria();     
<?php foreach($Columns as $Column):?>
     $Criterion=$this->getCriterion(<?php echo $peerBuilder->getColumnConstant($Column)?>);
     $<?php echo $Column->getPhpName()?>=$Criterion->getValue();
     $Criteria->add($Criterion);
<?php endforeach?>     
     if(!$this->equals($Criteria)){
         return;
     }
     $excuted=true;
     $CacheKey="Model:<?php echo $ObjectClassName?><?php foreach($Columns as $Column):?>-<?php echo $Column->getPhpName()?><?php endforeach ?>:<?php foreach($Columns as $Column):?>$<?php echo $Column->getPhpName()?><?php endforeach ?>";
     $Cache=$this->getTagcache();
     if($Object=$Cache->get($CacheKey)){
         return $Object;
     }
     if($Object=parent::findOne($con)){
         $Cache->set($CacheKey,$Object);
     }
     return $Object;
 }
