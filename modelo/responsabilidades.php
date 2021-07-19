<?php
include_once("../../controlador/objetopersistente.php");
/*
 * Created on 13/08/2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 class RespIva extends ObjetoPersistente{
     private $id;
     private $responsabilidad;


     public function _construct(){
     }
     
     public function getCampos(){
            return "responsabilidad";
     }
     
     public function getValoresCampos(){
            return $this->$responsabilidad;
     }

     protected static function getTable(){
            return "responsabilidadiva";
     }

     static public function responsabilidades($orden){
            return self::lista(self::getTable(), $orden);
     }


     static public function get($key){

     }
 }
?>
