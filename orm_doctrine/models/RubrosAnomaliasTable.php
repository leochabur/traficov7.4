<?php

/**
 * RubrosAnomaliasTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class RubrosAnomaliasTable extends Doctrine_Table
{
    /**
     * Returns an instance of this class.
     *
     * @return object RubrosAnomaliasTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('RubrosAnomalias');
    }
    
    public function findActivos($hydrationMode = null)
    {
           return Doctrine_Query::create()->select('r.*')->from('RubrosAnomalias  r')->execute();
    }
}