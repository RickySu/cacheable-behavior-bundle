

    /**
     * Get the associated <?php echo $className?> object
     *
     * @param  PropelPDO       $con Optional Connection object.
     * @return <?php           echo $className?> The associated <?php echo $className?> object.
     * @throws PropelException
     */
    public function get<?php echo $relCol?>(PropelPDO $con = null)
    {
         if ($Objects=$this->rebuild_get<?php echo $relCols?>(null,$con)) {
             return $Objects[0];
         }

         return null;
    }
