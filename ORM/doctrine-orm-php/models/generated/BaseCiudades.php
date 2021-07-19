<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Ciudades', 'doctrine');

/**
 * BaseCiudades
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_estructura
 * @property integer $id_provincia
 * @property string $ciudad
 * @property Estructuras $Estructuras
 * @property Provincias $Provincias
 * @property Doctrine_Collection $Cronogramas
 * @property Doctrine_Collection $Cronogramas_4
 * @property Doctrine_Collection $Cronogramas_5
 * @property Doctrine_Collection $Cronogramas_6
 * @property Doctrine_Collection $Ordenes
 * @property Doctrine_Collection $Ordenes_3
 * @property Doctrine_Collection $Ordenes_4
 * @property Doctrine_Collection $Ordenes_5
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseCiudades extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('ciudades');
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
        $this->hasColumn('id_provincia', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('ciudad', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
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
        $this->hasOne('Estructuras', array(
             'local' => 'id_estructura',
             'foreign' => 'id'));

        $this->hasOne('Provincias', array(
             'local' => 'id_provincia',
             'foreign' => 'id'));

        $this->hasMany('Cronogramas', array(
             'local' => 'id',
             'foreign' => 'ciudades_id_origen'));

        $this->hasMany('Cronogramas as Cronogramas_4', array(
             'local' => 'id_estructura',
             'foreign' => 'ciudades_id_estructura_origen'));

        $this->hasMany('Cronogramas as Cronogramas_5', array(
             'local' => 'id',
             'foreign' => 'ciudades_id_destino'));

        $this->hasMany('Cronogramas as Cronogramas_6', array(
             'local' => 'id_estructura',
             'foreign' => 'ciudades_id_estructura_destino'));

        $this->hasMany('Ordenes', array(
             'local' => 'id',
             'foreign' => 'id_ciudad_origen'));

        $this->hasMany('Ordenes as Ordenes_3', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructura_ciudad_origen'));

        $this->hasMany('Ordenes as Ordenes_4', array(
             'local' => 'id',
             'foreign' => 'id_ciudad_destino'));

        $this->hasMany('Ordenes as Ordenes_5', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructura_ciudad_destino'));
    }
}