<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Tipotnoordenes', 'doctrine');

/**
 * BaseTipotnoordenes
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id_orden
 * @property integer $id_estructura
 * @property integer $id_turno
 * @property integer $id_estructura_turno
 * @property integer $id_tipo_servicio
 * @property integer $id_estructura_tipo_servicio
 * @property string $i_v
 * @property Turnos $Turnos
 * @property Turnos $Turnos_2
 * @property Tiposervicio $Tiposervicio
 * @property Tiposervicio $Tiposervicio_4
 * @property Ordenes $Ordenes
 * @property Ordenes $Ordenes_6
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTipotnoordenes extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('tipotnoordenes');
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
        $this->hasColumn('id_estructura', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'default' => '0',
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_turno', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructura_turno', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_tipo_servicio', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructura_tipo_servicio', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('i_v', 'string', 1, array(
             'type' => 'string',
             'length' => 1,
             'fixed' => true,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Turnos', array(
             'local' => 'id_turno',
             'foreign' => 'id'));

        $this->hasOne('Turnos as Turnos_2', array(
             'local' => 'id_estructura_turno',
             'foreign' => 'id_estructura'));

        $this->hasOne('Tiposervicio', array(
             'local' => 'id_tipo_servicio',
             'foreign' => 'id'));

        $this->hasOne('Tiposervicio as Tiposervicio_4', array(
             'local' => 'id_estructura_tipo_servicio',
             'foreign' => 'id_estructura'));

        $this->hasOne('Ordenes', array(
             'local' => 'id_orden',
             'foreign' => 'id'));

        $this->hasOne('Ordenes as Ordenes_6', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructura'));
    }
}