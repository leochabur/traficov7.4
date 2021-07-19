<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('HistorialLicencias', 'doctrine');

/**
 * BaseHistorialLicencias
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_conductor
 * @property integer $id_licencia
 * @property date $vigencia_desde
 * @property date $vigencia_hasta
 * @property HistorialEmpleados $HistorialEmpleados
 * @property Licencias $Licencias
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseHistorialLicencias extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('historial_licencias');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('id_conductor', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_licencia', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('vigencia_desde', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0000-00-00',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('vigencia_hasta', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('HistorialEmpleados', array(
             'local' => 'id_conductor',
             'foreign' => 'id_empleado'));

        $this->hasOne('Licencias', array(
             'local' => 'id_licencia',
             'foreign' => 'id'));
    }
}