/**
 * Issue a SELECT query based on the current ModelCriteria
 * and format the list of results with the current formatter
 * By default, returns an array of model objects
 *
 * @param PropelPDO $con an optional connection object
 *
 * @return PropelObjectCollection|array|mixed the list of results, formatted by the current formatter
 */
public function find($con = null)
{
    $excuted=false;    
    foreach($this->getFindOneByUniqueIndexMethods() as $Method){
        $Object=$this->$Method($executed,$con);
        if($executed){
            if($Object){
                return array($Object);
            }
            return null;
        }
    }
    return parent::find($con);
}

/**
 * Issue a SELECT ... LIMIT 1 query based on the current ModelCriteria
 * and format the result with the current formatter
 * By default, returns a model object
 *
 * @param PropelPDO $con an optional connection object
 *
 * @return mixed the result, formatted by the current formatter
 */
public function findOne($con = null)
{
    $excuted=false;    
    foreach($this->getFindOneByUniqueIndexMethods() as $Method){
        $Object=$this->$Method($executed,$con);        
        if($executed){        
            return $Object;
        }
    }
    return parent::findOne($con);
}

protected function getFindOneByUniqueIndexMethods()
{
    static $Methods=array(<?php foreach($UniqueIndexs as $UniqueIndex):?>"findOneBy<?php foreach($UniqueIndex as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>WithCache",<?php endforeach?>);
    return $Methods;
}