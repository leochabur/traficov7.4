<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');

require_once ('../ws/ws-cliente.php'); // Libreria SOAP

function checkCliente($nombre, $clave){
 
	if ( ($nombre === "34314355") && ($clave === "chino") || 
		($nombre === "27007541") && ($clave === "leoch")){
		return true;
	}
	return false;
}

$postLoginOk = false;
$postLoginMsj = "Falló el login";  
 
if ($ClienteSesion->getSesionBd()){
	$postLoginOk = false;
	$postLoginMsj = "Sesión ya iniciada.";
}else{
	
	if (isset($_POST) && isset($_POST['cli'])){
	 
		$json_data = json_decode($_POST['cli']);
		if (!empty($json_data->nombre) && ($json_data->nombre !="") &&
			!empty($json_data->password) && ($json_data->password !=""))
		{
			 
			$wsLoginResult = wsClienteLogin($json_data->nombre, $json_data->password);
			$wsCodCliente = $wsLoginResult["ws_result"];
			$postLoginOk = $wsLoginResult["ws_ok"];
			$postMsj = $wsLoginResult["ws_msj"];
			if ($postLoginOk && !empty($wsCodCliente)){				
				 
				$ClienteSesion = new ClienteSesion(PREF_SESS_CLI,$wsCodCliente);
				$ClienteSesion->sesionIni();
				$ClienteSesion->guardarSesionBD();		
					
			}
			
			 
		}
		else{
			// error Campos
			$postLoginMsj = "Verifíque los datos enviados.";
		}
	}else{
		// error POST .
		$postLoginMsj = "No se enviaron datos.";
	}

}
	

$resultado = array("ok"=> $postLoginOk, "msj"=>$postLoginMsj);
 
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