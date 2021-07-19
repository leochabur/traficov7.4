<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Claseservicio', 'doctrine');

/**
 * BaseClaseservicio
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_estructura
 * @property string $clase
 * @property Estructuras $Estructuras
 * @property Doctrine_Collection $Cronogramas
 * @property Doctrine_Collection $Cronogramas_8
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseClaseservicio extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('claseservicio');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('id_estructura', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('clase', 'string', 95, array(
             'type' => 'string',
             'length' => 95,
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
        $this->hasOne('Estructuras', array(
             'local' => 'id_estructura',
             'foreign' => 'id'));

        $this->hasMany('Cronogramas', array(
             'local' => 'id',
             'foreign' => 'claseServicio_id'));

        $this->hasMany('Cronogramas as Cronogramas_8', array(
             'local' => 'id_estructura',
             'foreign' => 'claseServicio_id_estructura'));
    }
}