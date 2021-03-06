<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('RubrosAnomalias', 'doctrine');

/**
 * BaseRubrosAnomalias
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $rubro
  * @property date $fecha_creacion
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseRubrosAnomalias extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('rubros_anomalias');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('rubro', 'string', 105, array(
             'type' => 'string',
             'length' => 105,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('fecha_creacion', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        
    }
}
