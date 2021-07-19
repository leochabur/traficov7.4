<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');
 
require_once ('../sesion.php');
  
// Conexion
require_once ($raiz.RUTA_LIB . "clases/BdConexion.clase.php");
  
$postLoginOk = false;
$errorMsj = "Fallo el login";
 
if (isset($_POST) && isset($_POST['usuario'])){
 

	if (  $UsuarioSesion->getSesionBd())
	{
		$postLoginOk = true;
		$errorMsj = "";
	}
	
	$postUsuario = $_POST['usuario'];
	
	$resultadoLogin = Usuario::Login($postUsuario['username'], $postUsuario['password']);
	$resultadoLogin = json_decode($resultadoLogin);
	
	if ($resultadoLogin->ok){ 
		 
		// Login exitoso
		$fechahora = date("Y-m-d H:i:s");
		$UsuarioId = Usuario::getIdPorUsername($postUsuario['username']);
		if (isset($UsuarioId)){
			// Inicia sesión
			$UsuarioSesion = new UsuarioSesion(PREF_SESS_USER,$UsuarioId);
			$UsuarioSesion->sesionIni();
			$UsuarioSesion->guardarSesionBD();
			
			Usuario::updateUltSesion($UsuarioId);
			
			$postLoginOk = true;
			$errorMsj = "";
		}else{
			$errorMsj = "Error al cargar su cuenta de usuario.";
			$postLoginOk = false;
		}
		 
	}else{
		$errorMsj = $resultadoLogin->msj;
	}
		  
	  
}else{
	// error POST .
	$errorMsj = "Verifíque los datos enviados.";
}


$resultado = array("ok"=> $postLoginOk, "error"=>$errorMsj);
 
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