if(array_intersect(array(<?php foreach($Cols as $Col):?><?php echo $Col->getConstantName()?>,<?php endforeach?>),$this->getModifiedColumns())){
    $<?php echo $CacheObjectName?>=<?php echo $ObjectClassName?>Query::create()->findPk($this->getPrimaryKey());
}
