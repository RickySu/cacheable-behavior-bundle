"Tag:<?php echo $ObjectClassName?>#count<?php echo $relCol?><?php foreach($Keys as $Key):?>-<?php echo $Key->getName()?><?php endforeach ?>:"<?php foreach($Keys as $Key):?>."-'".addslashes($this<?php if(isset($Object)):?>->get<?php echo $Object?>()<?php endif?>->get<?php echo $Key->getPhpName()?>())."'"<?php endforeach?>