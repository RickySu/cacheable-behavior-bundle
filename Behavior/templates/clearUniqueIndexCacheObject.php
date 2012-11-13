<?php foreach($CacheKeys as $CacheKey):?>
<?php echo $CacheKey?>
$Cache->delete($CacheKey);
<?php endforeach?>
