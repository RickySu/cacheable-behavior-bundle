<?php if(isset($BeforeModify)&&$BeforeModify):?>
if (isset($<?php echo $CacheObjectName?>)) {
    $Cache->deleteTag(<?php echo $CacheTags?>);
}
<?php else:?>
$Cache->deleteTag(<?php echo $CacheTags?>);
<?php endif?>

