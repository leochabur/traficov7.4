<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Tipovencimiento', 'doctrine');

/**
 * BaseTipovencimiento
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $nombre
 * @property integer $id_estructura
 * @property Estructuras $Estructuras
 * @property Doctrine_Collection $Restclientetipovtvunidad
 * @property Doctrine_Collection $Restclientetipovtvunidad_3
 * @property Doctrine_Collection $Restriccioncliente
 * @property Doctrine_Collection $Restriccioncliente_7
 * @property Doctrine_Collection $Tipovencimientoporinterno
 * @property Doctrine_Collection $Vtosinternos
 * @property Doctrine_Collection $Vtosinternos_2
 * @property Doctrine_Collection $Vtvporcronograma
 * @property Doctrine_Collection $Vtvporcronograma_3
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseTipovencimiento extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('tipovencimiento');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('nombre', 'string', 45, array(
             'type' => 'string',
             'length' => 45,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
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
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Estructuras', array(
             'local' => 'id_estructura',
             'foreign' => 'id'));

        $this->hasMany('Restclientetipovtvunidad', array(
             'local' => 'id',
             'foreign' => 'id_tipo_vtv'));

        $this->hasMany('Restclientetipovtvunidad as Restclientetipovtvunidad_3', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructura_tipo_vtv'));

        $this->hasMany('Restriccioncliente', array(
             'local' => 'id',
             'foreign' => 'id_tipovtv'));

        $this->hasMany('Restriccioncliente as Restriccioncliente_7', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructuratipovtv'));

        $this->hasMany('Tipovencimientoporinterno', array(
             'local' => 'id',
             'foreign' => 'idtipovencimiento'));

        $this->hasMany('Vtosinternos', array(
             'local' => 'id',
             'foreign' => 'id_tipovtv'));

        $this->hasMany('Vtosinternos as Vtosinternos_2', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructuratipovtv'));

        $this->hasMany('Vtvporcronograma', array(
             'local' => 'id',
             'foreign' => 'id_vtv'));

        $this->hasMany('Vtvporcronograma as Vtvporcronograma_3', array(
             'local' => 'id_estructura',
             'foreign' => 'id_estructura_vtv'));
    }
}