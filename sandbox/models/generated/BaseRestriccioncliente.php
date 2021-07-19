<?php
// Connection Component Binding
Doctrine_Manager::getInstance()->bindComponent('Restriccioncliente', 'doctrine');

/**
 * BaseRestriccioncliente
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $id_estrutura
 * @property integer $id_cliente
 * @property integer $id_estructuracliente
 * @property integer $id_licencia
 * @property integer $id_estructuralicencia
 * @property integer $id_tipovtv
 * @property integer $id_estructuratipovtv
 * @property integer $id_tipounidad
 * @property integer $id_estructuratipounidad
 * @property string $restringe
 * @property Estructuras $Estructuras
 * @property Clientes $Clientes
 * @property Clientes $Clientes_3
 * @property Licencias $Licencias
 * @property Licencias $Licencias_5
 * @property Tipovencimiento $Tipovencimiento
 * @property Tipovencimiento $Tipovencimiento_7
 * @property Tipounidad $Tipounidad
 * @property Tipounidad $Tipounidad_9
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseRestriccioncliente extends Doctrine_Record
{
    public function setTableDefinition()
    {
        $this->setTableName('restriccioncliente');
        $this->hasColumn('id', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('id_estrutura', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_cliente', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructuracliente', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => true,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_licencia', 'integer', 8, array(
             'type' => 'integer',
             'length' => 8,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructuralicencia', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => true,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_tipovtv', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructuratipovtv', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_tipounidad', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('id_estructuratipounidad', 'integer', 4, array(
             'type' => 'integer',
             'length' => 4,
             'fixed' => false,
             'unsigned' => false,
             'primary' => false,
             'notnull' => false,
             'autoincrement' => false,
             ));
        $this->hasColumn('restringe', 'string', 2, array(
             'type' => 'string',
             'length' => 2,
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
             'local' => 'id_estrutura',
             'foreign' => 'id'));

        $this->hasOne('Clientes', array(
             'local' => 'id_cliente',
             'foreign' => 'id'));

        $this->hasOne('Clientes as Clientes_3', array(
             'local' => 'id_estructuracliente',
             'foreign' => 'id_estructura'));

        $this->hasOne('Licencias', array(
             'local' => 'id_licencia',
             'foreign' => 'id'));

        $this->hasOne('Licencias as Licencias_5', array(
             'local' => 'id_estructuralicencia',
             'foreign' => 'id_estructura'));

        $this->hasOne('Tipovencimiento', array(
             'local' => 'id_tipovtv',
             'foreign' => 'id'));

        $this->hasOne('Tipovencimiento as Tipovencimiento_7', array(
             'local' => 'id_estructuratipovtv',
             'foreign' => 'id_estructura'));

        $this->hasOne('Tipounidad', array(
             'local' => 'id_tipounidad',
             'foreign' => 'id'));

        $this->hasOne('Tipounidad as Tipounidad_9', array(
             'local' => 'id_estructuratipounidad',
             'foreign' => 'id_estructura'));
    }
}