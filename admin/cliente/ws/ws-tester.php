<?php
$raiz = "../../";
require_once ($raiz.'base.inc.php');

require_once ('ws-cliente.php'); // Libreria SOAP

//$wsLoginResult = wsClienteLogin(27007541, 27007541);
//var_dump($wsLoginResult);

/*
$oSoapSClient = new nusoap_client('http://leochabur.linkpc.net/ws/ws_redes.php?wsdl', true);
  
$paramsServicios = array( "fecha_servicio"=>array(
			"dia"=>"23",
			"mes"=>"08",
			"anio"=>"2015"));
			
//$result = $oSoapSClient->call('listaServicios', $paramsServicios );
 
 $json = '{"cod_serv":"188","subida":"1","bajada":"1","precio":"80","asientos":"1"}';
 $jsonReserva = json_decode($json);
		 
	$codCliente = (int)$Cliente->getClienteId();
	$codServicio =  $jsonReserva->cod_serv;
	$lugarSubida =  $jsonReserva->subida;
	$lugarBajada =  $jsonReserva->bajada;
	$precioPasaje =  $jsonReserva->precio;
	
 $paramsReserva = array( "datosreserva"=>array(
			"codigoCliente"=>32,
			"codigoServicio"=>62,
			"lugarSubida"=>1,
			"lugarBajada"=>1,
			"precioPasaje"=> 100));
			
//$result = $oSoapSClient->call('realizarReserva', $paramsReserva );
	$wsReserva = wsClienteAltaReserva($codCliente, $codServicio, $lugarSubida, $lugarBajada, $precioPasaje);

$params = array("datos_consulta_reserva"=>array(
			"codigoCliente"=>1,
			"dia"=>23,
			"mes"=>08,
			"anio"=>2015));
//$result = $oSoapSClient->call('listaServicios', $paramsReserva );

 $ws_error = $oSoapSClient->getError(); 
  
 var_dump ($wsReserva);
 var_dump ($ws_error); 
 */
 
 $wsClienteReservas = wsClienteGetReservas(1034, 21, 08, 2015);
	$clienteReservas = $wsClienteReservas["ws_result"];
 var_dump($clienteReservas);