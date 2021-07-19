<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Empleadoresporestructura', 'doctrine');

/**
 * BaseEmpleadoresporestructura
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_empleador
 * @property integer $id_estructura
 * @property Empleadores $Empleadores
 * @property Estructuras $Estructuras
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEmpleadoresporestructura extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('empleadoresporestructura');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('id_empleador', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructura', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
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
        $this->hasOne('Empleadores', array(
             'local' => 'id_empleador',
             'foreign' => 'id'));

        $this->hasOne('Estructuras', array(
             'local' => 'id_estructura',
             'foreign' => 'id'));
    }
}