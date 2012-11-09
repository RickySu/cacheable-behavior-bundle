/**
 * Handle the magic
 * Supports findByXXX(), findOneByXXX(), filterByXXX(), orderByXXX(), and groupByXXX() methods,
 * where XXX is a column phpName.
 * Supports XXXJoin(), where XXX is a join direction (in 'left', 'right', 'inner')
 */
public function __call($name, $arguments)
{
    $excuted = false;    
<?php foreach($MagicMethodCall as $Call):?>
    $return = $this->findOneBy<?php foreach($Call as $Column):?><?php echo $Column->getPhpName()?><?php endforeach?>WithCache($excuted, $name, $arguments);
    if($excuted){
        return $return;
    }
<?php endforeach?>
    return parent::__call($name, $arguments);
}
