<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Especialidades', 'doctrine');

/**
 * BaseEspecialidades
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $especialidad
 * @property Doctrine_Collection $Certmedicos
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEspecialidades extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('especialidades');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('especialidad', 'string', 155, array(
             'type' => 'string',
             'length' => 155,
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
        $this->hasMany('Certmedicos', array(
             'local' => 'id',
             'foreign' => 'id_especialidad'));
    }
}