<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');
  
function checkClienteMail($email){
 
	$test_data_email = "leo@santarita.com";
	if ( ($test_data_email === $email)){
		return true;
	}
	return false;
}

$postEnvioOk = false;
$postEnvioMsj = "Fallo el envío de tu nueva clave";  
  
if ($ClienteSesion->getSesionBd()){
	$postEnvioOk = false;
	$postEnvioMsj = "Sesión ya iniciada.";
}else{
	
	if (isset($_POST) && isset($_POST['cli'])){
	 
		$json_data = json_decode($_POST['cli']);
		 
	 	// valida campos
	 	if (empty($json_data->email)){
	 		$postEnvioOk = false;
	 		$postEnvioMsj = "Faltan ingresar datos.";
	 	}else{
	 		
	 		$checkCliente = false;
			if ( checkClienteMail($json_data->email) ){		 
				// Login exitoso
				$postEnvioOk = true;
				$postEnvioMsj = "Tu nueva clave se envío a tu dirección de correo!";
				 		
				 			
			}else{
				$postEnvioMsj = "Su nombre de cliente o dirección correo son inválidos.";
			}
	 	}
		  
	}else{
		// error POST .
		$postLoginMsj = "Verifíque los datos enviados.";
	}

}


$resultado = array("ok"=> $postEnvioOk, "msj"=>$postEnvioMsj);
 
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