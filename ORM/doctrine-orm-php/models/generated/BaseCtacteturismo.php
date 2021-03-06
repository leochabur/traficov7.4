<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Ctacteturismo', 'doctrine');

/**
 * BaseCtacteturismo
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_orden
 * @property integer $id_estructura_orden
 * @property integer $id_cliente
 * @property integer $id_estructura_cliente
 * @property float $importe
 * @property string $viaje_pago
 * @property timestamp $fecha_ingreso
 * @property integer $id_user
 * @property Clientes $Clientes
 * @property Clientes $Clientes_2
 * @property Usuarios $Usuarios
 * @property OrdenesTurismo $OrdenesTurismo
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCtacteturismo extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ctacteturismo');
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
             'unsigned' => true,
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
        $this->hasColumn('id_cliente', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructura_cliente', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('importe', 'float', null, array(
             'type' => 'float',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('viaje_pago', 'string', 1, array(
             'type' => 'string',
             'length' => 1,
             'fixed' => true,
             'unsigned' => false,
             'primary' => false,
             'default' => '',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('fecha_ingreso', 'timestamp', null, array(
             'type' => 'timestamp',
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0000-00-00 00:00:00',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_user', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Clientes', array(
             'local' => 'id_cliente',
             'foreign' => 'id'));

        $this->hasOne('Clientes as Clientes_2', array(
             'local' => 'id_estructura_cliente',
             'foreign' => 'id_estructura'));

        $this->hasOne('Usuarios', array(
             'local' => 'id_user',
             'foreign' => 'id'));

        $this->hasOne('OrdenesTurismo', array(
             'local' => 'id_orden',
             'foreign' => 'id'));
    }
}