<?php
include_once("../../controlador/objetopersistente.php");
/*
 * Created on 13/08/2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 class Provincia extends ObjetoPersistente{

     private $nombre;

     protected static function getTable(){
            return "Provincias";
     }
     
     public function getCampos(){
            return "nombre";
     }

     public function getValoresCampos(){
            return $this->$nombre;
     }
     
     static public function provincias($orden){
            return self::lista(self::getTable(), $orden);
     }
     
     static public function get($key){
     
     }
 }
?>
