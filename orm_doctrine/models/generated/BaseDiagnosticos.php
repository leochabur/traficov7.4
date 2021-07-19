<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Diagnosticos', 'doctrine');

/**
 * BaseDiagnosticos
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $diagnostico
 * @property Doctrine_Collection $Certmedicos
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseDiagnosticos extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('diagnosticos');
        $this->hasColumn('id', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('diagnostico', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
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
             'foreign' => 'id_diagnostico'));
    }
}