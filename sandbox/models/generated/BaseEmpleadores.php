<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Empleadores', 'doctrine');

/**
 * BaseEmpleadores
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $razon_social
 * @property string $direccion
 * @property string $cuit_cuil
 * @property integer $id_localidad
 * @property string $telefono
 * @property string $mail
 * @property string $www
 * @property integer $id_resp_iva
 * @property integer $activo
 * @property integer $id_estructura
 * @property string $color
 * @property Estructuras $Estructuras
 * @property Doctrine_Collection $Empleadoresporestructura
 * @property Doctrine_Collection $Empleados
 * @property Doctrine_Collection $Empleados_2
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEmpleadores extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('empleadores');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('razon_social', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('direccion', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('cuit_cuil', 'string', 20, array(
             'type' => 'string',
             'length' => 20,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_localidad', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('telefono', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mail', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('www', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_resp_iva', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('activo', 'integer', 1, array(
             'type' => 'integer',
             'length' => 1,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '1',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructura', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'default' => '1',
             'autoincrement' => false,
             ));
        $this->hasColumn('color', 'string', 6, array(
             'type' => 'string',
             'length' => 6,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => 'FF0000',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Estructuras', array(
             'local' => 'id_estructura',
             'foreign' => 'id'));

        $this->hasMany('Empleadoresporestructura', array(
             'local' => 'id',
             'foreign' => 'id_empleador'));

        $this->hasMany('Empleados', array(
             'local' => 'id',
             'foreign' => 'id_empleador'));

        $this->hasMany('Empleados as Empleados_2', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructura_empleador'));
    }
}