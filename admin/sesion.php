<?php 
 
if (!isset($_SESSION))
{
	session_start();
}

// Sesion
require_once ($raiz.RUTA_LIB . "clases/UsuarioSesion.clase.php");

$usuarioLogueado= false;
$redirLogin 	= 'login.php'; 
$redirIndex		= 'index.php';

$UsuarioSesion = new UsuarioSesion(PREF_SESS_USER);

if ($UsuarioSesion->getSesionBd()){
	$Usuario = Usuario::cargarPorId($UsuarioSesion->getIdUsuario());
	
}