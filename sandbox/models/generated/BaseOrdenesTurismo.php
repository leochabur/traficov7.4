<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('OrdenesTurismo', 'doctrine');

/**
 * BaseOrdenesTurismo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_orden
 * @property float $precio_venta_neto
 * @property float $precio_venta_final
 * @property float $viaticos
 * @property integer $id_estructura_orden
 * @property string $contacto
 * @property string $mail_contacto
 * @property string $tel_contacto
 * @property string $lugar_salida
 * @property string $lugar_llegada
 * @property date $fecha_regreso
 * @property integer $capacidad_solicitada
 * @property time $hora_regreso
 * @property Ordenes $Ordenes
 * @property Ordenes $Ordenes_2
 * @property Doctrine_Collection $Ctacteturismo
 * @property Doctrine_Collection $Pagosturismo
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseOrdenesTurismo extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ordenes_turismo');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('id_orden', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('precio_venta_neto', 'float', null, array(
             'type' => 'float',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('precio_venta_final', 'float', null, array(
             'type' => 'float',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('viaticos', 'float', null, array(
             'type' => 'float',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructura_orden', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('contacto', 'string', 155, array(
             'type' => 'string',
             'length' => 155,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('mail_contacto', 'string', 145, array(
             'type' => 'string',
             'length' => 145,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('tel_contacto', 'string', 55, array(
             'type' => 'string',
             'length' => 55,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('lugar_salida', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('lugar_llegada', 'string', 255, array(
             'type' => 'string',
             'length' => 255,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('fecha_regreso', 'date', null, array(
             'type' => 'date',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('capacidad_solicitada', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('hora_regreso', 'time', null, array(
             'type' => 'time',
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
        $this->hasOne('Ordenes', array(
             'local' => 'id_orden',
             'foreign' => 'id'));

        $this->hasOne('Ordenes as Ordenes_2', array(
             'local' => 'id_estructura_orden',
             'foreign' => 'id_estructura'));

        $this->hasMany('Ctacteturismo', array(
             'local' => 'id',
             'foreign' => 'id_orden'));

        $this->hasMany('Pagosturismo', array(
             'local' => 'id',
             'foreign' => 'id_orden_turismo'));
    }
}