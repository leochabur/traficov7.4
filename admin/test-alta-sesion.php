<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
require_once ($raiz.RUTA_LIB . 'clases/UsuarioSesion.clase.php');
   
$sesion = new UsuarioSesion(1);

$sesion->sesionIni();
$sesion->guardarSesion();

var_dump($sesion);
