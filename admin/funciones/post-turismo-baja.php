<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');
 
require_once ('../sesion.php');
 
if (!$UsuarioSesion->getSesionBd()){
	header("Location: $redirLogin");
}

// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
 
$postBajaOk = false;
$errorMsj = "";
if (isset($_POST) && $_POST['turismo']){
 
	$postTurismo = $_POST['turismo'];
	 
	$bd = new BdConexion();
	// Actualizo Fecha de última sesion del usuario
	$query = "UPDATE turismo SET FechaBaja = NOW() WHERE Id = :id ; ";
	$bd->query($query);
	$bd->bind(':id', $postTurismo); 
	try{
		$bd->execute();
		$postBajaOk = true;
	}catch (Exception $ex){
		$postBajaOk = false;	
		$errorMsj = "No se pudo realizar la acción.";
		
	}
 
	$bd = null;
	  
}else{
	// error POST .
	$errorMsj = "Verifíque los datos enviados.";
}


$resultado = array("ok"=> $postBajaOk, "error"=>$errorMsj);
 
header("Content-type: application/json; charset=UTF-8" );
$etag = md5(serialize($resultado));

if ((isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) && ($forzar==false))
{
	header('notmodified',true,304);
}
else
{
	header("Etag: ".$etag);
	echo json_encode($resultado);

}

?>