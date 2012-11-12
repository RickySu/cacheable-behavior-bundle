<?php if($CacheKeys):?>
$TagCache=$this->getTagcache();
<?php foreach($CacheKeys as $CacheKey):?>
<?php echo $CacheKey?>
$TagCache->delete($CacheKey);
<?php endforeach?>
<?php endif?>