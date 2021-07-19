<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
require_once ($raiz.RUTA_LIB . 'clases/UsuarioSesion.clase.php');
   

$sesion = new UsuarioSesion();

if (!$sesion->getSesionBd()){
	echo "no sesion";
}else{
	$sesion->sesionFin();
	var_dump($_SESSION);
	
}
 
 var_dump($sesion);
