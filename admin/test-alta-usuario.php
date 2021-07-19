<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
require_once ($raiz.RUTA_LIB . 'clases/Seguridad.clase.php');
 
$bd = new BdConexion();
/*
// Actualizo Fecha de última sesion del usuario
$query = "INSERT INTO usuario (Nombre, Apellido, Email, Username, Pass, Hash, Activo, FechaAlta, FechaEdit) 
		VALUES (:nombre, :apellido, :email, :user, :pass, :hash, :activo, NOW(), NOW()); ";
$bd->query($query);
$bd->bind(':nombre', "Leonardo");
$bd->bind(':apellido', "Chabur");
$bd->bind(':email', "leochabur@gmail.com");
$bd->bind(':user', "leoch");
$bd->bind(':pass', $pass);
$bd->bind(':hash', $hash);
$bd->bind(':activo', true);
$bd->execute();

$IdUser = $bd->lastInsertId();
var_dump($IdUser);
*/

$sql  = "UPDATE usuario SET Pass =:password WHERE Id = :id ; ";
$bd->query($sql);
$bd->bind(":password", Seguridad::hash_password('admin'));
$bd->bind(":id", 1);
try{
	$bd->execute();
	$resultpostpass = true;
} catch (Exception $ex){
	$msjpostpass = "Sus datos no se pudieron actualizar.";
	$resultpostpass = false;
}
	
$bd = null;