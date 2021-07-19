<?php
include("bdamin.php");
/*
 * Created on 13/08/2012
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

 abstract class ObjetoPersistente {

     public abstract function getCampos();
     public abstract function getValoresCampos();
       
       protected static function lista($tabla, $orden){
                 $query = "select * from $tabla order by $orden";
                 return BdAdmin::getInstancia()->ejecutar($query);
       }
       
       public function guardar(){
             // $query = "insert into "+
       }
 }
?>

