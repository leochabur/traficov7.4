<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
require_once ($raiz.RUTA_LIB . 'clases/Seguridad.clase.php');
require_once ($raiz.RUTA_LIB . 'clases/UsuarioLogin.clase.php');

$hash = Seguridad::randomSalt();
$pass = Seguridad::create_hash($hash."leocsr15");

$UsuarioLogin = new UsuarioLogin("leoch", "leocsr15");
 
if ($UsuarioLogin->Login()){

	echo "OK";
	
	// Login exitoso
	$fechahora = date("Y-m-d H:i:s");
	// Inicia sesión
	$UsuarioSesion = new UsuarioSesion($UsuarioLogin->getUserId());
	$UsuarioSesion->sesionIni();
	$UsuarioSesion->guardarSesion();
	
	$bd = new BdConexion();
	// Actualizo Fecha de última sesion del usuario
	$query = "UPDATE usuario SET FechaUltSesion = NOW() WHERE Id = :id ; ";
	$bd->query($query);
	$bd->bind(':id', $UsuarioLogin->getUserId()); 
	$bd->execute();
	$bd = null;
	$postLoginOk = true;
	

}else{
	$errorMsj = $UsuarioLogin->getError();
} 

var_dump($UsuarioLogin);