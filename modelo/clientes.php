<?php
include_once("../../controlador/objetopersistente.php");
/*
 * Created on 13/08/2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class Cliente extends ObjetoPersistente{

     private $id, $estructura, $razon_social, $direccion, $telefono, $activo, $respIva;

     protected static function getTable(){
            return "Clientes";
     }

     public function getCampos(){
            return "id_estructura, razon_social, direccion, telefono, activo, id_responsabilidadIva";
     }

     public function getValoresCampos(){
            return $this->$estructura.", '".$this->$razon_social."', '".$this->$direccion."', '".$this->$telefono."', ".$this->$activo.",".$this->$respIva;
     }

     static public function clientes($orden){
            return self::lista(self::getTable(), $orden);
     }

     static public function get($key){

     }
 }
?>
