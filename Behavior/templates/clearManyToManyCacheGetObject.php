//clear cross reference table cache.
$Cache->deleteTag($this->get<?php echo $crossRelCols?>CacheTag());
foreach ($this->get<?php echo $crossRelCols?>() as $crossObject) {
    $CacheTag="Tag:<?php echo $ObjectClassName?>#get<?php echo $relCol?><?php foreach($Keys as $Key):?>-<?php echo $Key->getName()?><?php endforeach ?>:"<?php foreach($Cols as $Col):?>."-'".addslashes($crossObject->get<?php echo $Col->getPhpName()?>())."'"<?php endforeach?>;
    $Cache->deleteTag($CacheTag);
}
