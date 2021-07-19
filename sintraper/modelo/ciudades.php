<?php
include_once("../../controlador/objetopersistente.php");
/*
 * Created on 13/08/2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class Ciudad extends ObjetoPersistente{
     private $id;
     private $ciudad;
     private $id_provincia;
     private $id_estructura;

     public function _construct(){
     }
     
     public function getCampos(){
            return "ciudad, id_provincia, id_estructura";
     }
     
     public function getValoresCampos(){
            return $this->$ciudad.",".$this->$id_provincia.",".$this->$id_estructura;
     }

     protected static function getTable(){
            return "Ciudades";
     }

     static public function ciudades($orden){
            return self::lista(self::getTable(), $orden);
     }


     static public function get($key){

     }
 }
?>
