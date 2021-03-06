<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Francosdiarios', 'doctrine');

/**
 * BaseFrancosdiarios
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property date $fecha
 * @property integer $id_empleado
 * @property integer $id_novedad
 * @property integer $id_user
 * @property timestamp $fecha_mod
 * @property Empleados $Empleados
 * @property CodNovedades $CodNovedades
 * @property Usuarios $Usuarios
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseFrancosdiarios extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('francosdiarios');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('fecha', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_empleado', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_novedad', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_user', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('fecha_mod', 'timestamp', null, array(
             'type' => 'timestamp',
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
        $this->hasOne('Empleados', array(
             'local' => 'id_empleado',
             'foreign' => 'id_empleado'));

        $this->hasOne('CodNovedades', array(
             'local' => 'id_novedad',
             'foreign' => 'id'));

        $this->hasOne('Usuarios', array(
             'local' => 'id_user',
             'foreign' => 'id'));
    }
}