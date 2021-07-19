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

//$json_data = json_decode($_GET['itemTurismo']);
$IdTurismo = $_GET['itemTurismo']; //$json_data->id;
$opciones = array(
		"IdTurismo"=>$IdTurismo, 
		"upload_url"=>URL_WEB.RUTA_UPLOADS."turismo/$IdTurismo/",
		"raiz" =>$raiz,
		"upload_dir"=>RUTA_UPLOADS."turismo/$IdTurismo/",
		"url_script_del" => "admin/funciones/post-turismo-img-del.php"
);
 
$upload_handler = new TurismoImgUploadHandler($opciones);
 
 

?>
