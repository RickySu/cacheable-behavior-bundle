"Tag:<?php echo $ObjectClassName?>#count<?php echo $relCol?><?php foreach($Keys as $Key):?>-<?php echo $Key->getName()?><?php endforeach ?>:"<?php foreach($Cols as $Col):?>."-'".addslashes(<?php if(isset($BeforeModify)&&$BeforeModify):?>$<?php echo $CacheObjectName?><?php else:?>$this<?php endif ?>->get<?php echo $Col->getPhpName()?>())."'"<?php endforeach?>
