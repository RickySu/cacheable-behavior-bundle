
/**
 * find a object by<?php foreach($Columns as $Column):?> <?php echo $Column->getPhpName()?><?php endforeach ?>.
 *
 * @param      $name
 * @param      $arguments
 * @return     <?php echo $queryBuilder->getObjectClassname()?>
 */
 protected function findOneBy<?php foreach ($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>WithCache(&$excuted, PropelPDO $con=null) {
     $Criteria = new \Criteria($this->getDBName());
<?php foreach($Columns as $Column):?>
     if (isset($this->map[<?php echo $queryBuilder->getPeerBuilder()->getColumnConstant($Column)?>])) {
         $Criterion=$this->map[<?php echo $queryBuilder->getPeerBuilder()->getColumnConstant($Column)?>];
         $<?php echo $Column->getName()?>=$Criterion->getValue();
         $Criteria->add($Criterion);
     }
<?php endforeach?>
     if (!$this->equals($Criteria)) {
         return;
     }
     $excuted=true;
     <?php echo $CacheKey?>
     $Cache=$this->getTagcache();
     if ($Object=$Cache->get($CacheKey)) {
         return $Object;
     }
     if ($Object=parent::findOne($con)) {
         $Cache->set($CacheKey,$Object);
     }

     return $Object;
 }
