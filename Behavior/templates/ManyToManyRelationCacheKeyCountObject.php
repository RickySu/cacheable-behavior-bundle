"Model:<?php echo $ObjectClassName?>#count<?php echo $relCol?>#distinct:".($distinct?'1':'0')."#{$CriteriaHash}<?php foreach($Keys as $Key):?>-<?php echo $Key->getName()?><?php endforeach ?>:"<?php foreach($Cols as $Col):?>."-'".addslashes($this-><?php echo $Col->getName()?>)."'"<?php endforeach?>