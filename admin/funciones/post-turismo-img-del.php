<?php
$raiz = "../";
require_once ($raiz.'base.inc.php');
 
require_once ('../sesion.php');
 
if (!$UsuarioSesion->getSesionBd()){
//	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
require_once ("TurismoImgUploadHandler.php");

$IdTurismo = $_GET['itemTurismo'];
$IdImg =  $_GET['idImg'];
$opciones = array(
		"IdTurismo"=>$IdTurismo,
		"IdImg"=>$IdImg, 
		"upload_url"=>URL_WEB.RUTA_UPLOADS."turismo/$IdTurismo/",
		"raiz" =>$raiz,
		"upload_dir"=>RUTA_UPLOADS."turismo/$IdTurismo/");
 
$upload_handler = new TurismoImgUploadHandler($opciones);
 
 

?>
