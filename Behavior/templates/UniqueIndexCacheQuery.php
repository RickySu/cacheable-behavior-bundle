
/**
 * find a object by<?php foreach($Columns as $Column):?> <?php echo $Column->getPhpName()?><?php endforeach ?>.
 *
 * @param      $name
 * @param      $arguments
 * @return     <?php echo $ObjectClassName?> 
 */
 protected function findOneBy<?php foreach($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>WithCache(&$excuted, $name, $arguments)
 {
     if( ('findOneBy<?php foreach($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>' != $name)  && 
         ('findBy<?php foreach($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>' != $name) ){
         $Criteria=new \Criteria();
<?php foreach($Columns as $Column):?>
         $Criteria->add(<?php echo $peerBuilder->getColumnConstant($Column)?>,$<?php echo $Column->getName()?>);
<?php endforeach?>
         if(!$this->equals($Criteria)){
             return;
         }
     }
     $FindOne=($MethodIndex>=0);
     $excuted=true;
     $CacheKey="Model:<?php echo $ObjectClassName?><?php foreach($Columns as $Column):?>-<?php echo $Column->getPhpName()?><?php endforeach ?>:<?php foreach($Columns as $Column):?>$<?php echo $Column->getName()?><?php endforeach ?>";
     $Cache=$this->getTagcache();
     if($Object=$Cache->get($CacheKey)){
         if($FindOne){
             return $Object;
         }
         return array($Object);
     }
     if($Object=call_user_func_array('parent::findOneBy<?php foreach($Columns as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>', $arguments)){
         $Cache->set($CacheKey,$Object);
     }
     if($FindOne){
         return $Object;
     }
     return array($Object);
 }
