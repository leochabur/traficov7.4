<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');


 if (!$ClienteSesion->getSesionBd()){
	header("Location: $redirCliLogin");
}

require_once ('../ws/ws-cliente.php'); // Libreria SOAP
 
$postOk = false;
$postMsj = "Falló la solicitud de reserva.";  

 
if (isset($_POST) && isset($_POST['postReserva'])){
 
	$jsonReserva = json_decode($_POST['postReserva']);
		 
	$codCliente 		= $Cliente->getClienteId();
	$codServicio 		= $jsonReserva->cod_serv;
	$lugarSubida 		= 12; //$jsonReserva->subida;
	$lugarBajada 		= 17; //$jsonReserva->bajada;
	$precioPasaje 		= $jsonReserva->precio;
	$asientosReserva 	= $jsonReserva->asientos;
	
	for($i=1; $i<=$asientosReserva; $i++){
		$wsReserva = wsClienteAltaReserva($codCliente, $codServicio, $lugarSubida, $lugarBajada, $precioPasaje);
	}
	
	$postMsj = $wsReserva["ws_result"];	
	$postOk	 = $wsReserva["ws_ok"];
 
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