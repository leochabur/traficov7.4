<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');


 if (!$ClienteSesion->getSesionBd()){
	header("Location: $redirCliLogin");
}

require_once ('../ws/ws-cliente.php'); // Libreria SOAP
 
$postOk = false;
$postMsj = "Falló la cancelación de la reserva.";  

 
if (isset($_POST) && isset($_POST['postCodReserva'])){
 
	  
	$codReserva = $_POST['postCodReserva'];
	
	$wsReserva = wsClienteBajaReserva($codReserva);
	$postMsj = $wsReserva["ws_result"];	
	$postOk	 = $wsReserva["ws_ok"];
	if (!$postOk){	 
		$postMsj = $wsReserva["ws_error"];
	}
	  
}else{
	// error POST .
	$postMsj = "Verifíque los datos enviados.";
}


$resultado = array("ok"=> $postOk, "msj"=>$postMsj);
 
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