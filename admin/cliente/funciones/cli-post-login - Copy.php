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
		if (!empty($json_data->nombre) && ($json_data->nombre =="") &&
			!empty($json_data->password) && ($json_data->password ==""))
		{
			
			$resultadoLogin = Cliente::Login($json_data->nombre, $json_data->password);
			$resultadoLogin = json_decode($resultadoLogin);
			if ($resultadoLogin->ok){
	 	 
				// PRUEBA
				$checkCliente = false;
				if ( checkCliente($json_data->nombre,$json_data->password) ){	
		
					$ClienteId = Cliente::getIdPorUsername($json_data->nombre);
					if (isset($ClienteId)){
						// Inicia sesión
						$ClienteSesion = new ClienteSesion(PREF_SESS_CLI,$ClienteId);
						$ClienteSesion->sesionIni();
						$ClienteSesion->guardarSesionBD();
							
						// Login exitoso
						$postLoginOk = true;
						$postLoginMsj = "Ok!";
						
					}else{
						$postLoginMsj = "Error al cargar su cuenta.";
						$postLoginOk = false;
					}
					 
				}else{
					$postLoginMsj = "Cliente no existe";
				}
			}else{
				$postLoginMsj = $resultadoLogin->msj;
			}
			/*
			$wsLoginResult = wsClienteLogin($json_data->nombre, $json_data->password);
			$wsCodCliente = $wsLoginResult["ws_result"];
			$postLoginOk = $wsLoginResult["ws_ok"];
			if (!$wsLoginResult["ws_ok"];){					 
				$postMsj = $wsReserva["ws_result"];
			}
			
			if ($resultadoLogin->ok){
	 	 
				// PRUEBA
				$checkCliente = false;
				if ( checkCliente($json_data->nombre,$json_data->password) ){	
		
					$ClienteId = Cliente::getIdPorUsername($json_data->nombre);
					if (isset($ClienteId)){
						// Inicia sesión
						$ClienteSesion = new ClienteSesion(PREF_SESS_CLI,$ClienteId);
						$ClienteSesion->sesionIni();
						$ClienteSesion->guardarSesionBD();
							
						// Login exitoso
						$postLoginOk = true;
						$postLoginMsj = "Ok!";
						
					}else{
						$postLoginMsj = "Error al cargar su cuenta.";
						$postLoginOk = false;
					}
					 
				}else{
					$postLoginMsj = "Cliente no existe";
				}
			}else{
				$postLoginMsj = $resultadoLogin->msj;
			}
			*/
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