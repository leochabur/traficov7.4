<?php

require_once ($raiz.RUTA_LIB.'soap/nusoap.php'); // Libreria SOAP

function wsGetSoapCli(){
	return new nusoap_client('https://leochabur.linkpc.net/ws/ws_redes.php?wsdl', true);
}

/* 
 ****************************************************
 *	Funcion Login Cliente 
 ****************************************************
 *  Parámetros
 *  @datos_usuario: array():	
 *	@dni: string 
 *	@clave: string 
 * 
 *	Devuelve codigo de cliente, si existe y es correcto
 * 
 *	Devuelve 0 si no existe.
 *
 */
function wsClienteLogin($cliente_dni, $cliente_clave){

	$resultado = array(
				"ws_ok"=>false, 
				"ws_result"=>null, 
				"ws_msj"=>"", 
				"ws_error"=>"");
	$params = array( "usuario"=>array(
			"documento" => $cliente_dni,
			"clave" => $cliente_clave));

	$resultado["ws_ok"] = false;
	$resultado["ws_msj"] = "Usuario y/o clave no son válidos.";
	try{

		// Instancia Cliente
		$oSoapSClient = wsGetSoapCli();
		$resultado["ws_error"] = $oSoapSClient->getError();
		$resultado["ws_result"]	= $oSoapSClient->call('loginUsuario', $params ); 
		if (!empty ($resultado["ws_result"]) && $resultado["ws_result"] > 0){
			$resultado["ws_ok"] = true;
			$resultado["ws_msj"] = "Login Ok!";
		}		
		
			  
	}catch (Exception $exc){
		$resultado["ws_error"] = "Excepcion en el servicio web. "  ;
		$resultado["ws_ok"] = false;
		$resultado["ws_msj"] = "No se pudo validar su usuario.";
	}

	return $resultado;
}

/*
 ****************************************************
 *	Funcion Listado de Servicios  
 ****************************************************
 *  Parámetros
 *  @fecha_servicio: array():	
 *	@dia: int 
 *	@mes: int 
 *	@anio: int
 * 
 *	Devuelve los servicios disponibles para la fecha dada, si existen, en un arreglo:
 *  @datos_servicio: 
 *	@hora: string
 *	@origen: string
 *	@destino: string
 *	@precio: string
 *	@cantAsientos: int
 * 	@codigo_serv: string
 * 
 *	Devuelve null si no existe.
 *
 */
 function wsClienteGetServicios( $dia, $mes, $anio){
 
	$resultado = array(
				"ws_ok"=>false, 
				"ws_result"=>null, 
				"ws_msj"=>"", 
				"ws_error"=>"");
				
	$params = array( "fecha_servicio"=>array(
			"dia" => $dia,
			"mes" => $mes,
			"anio" => $anio));

	try{

		// Instancia Cliente
		$oSoapSClient = wsGetSoapCli();
		$resultado["ws_error"] = $oSoapSClient->getError();
		$resultado["ws_result"]	= $oSoapSClient->call('listaServicios', $params ); 
		$resultado["ws_ok"] = true;
		$resultado["ws_msj"] = "Servicios Ok!.";
			  
	}catch (Exception $exc){
		$resultado["ws_error"] = "Excepcion en el servicio web. "  ;
		$resultado["ws_ok"] = false;
		$resultado["ws_msj"] = "No se pudo obtener el listado de servicios.";
	}

	return $resultado;
}

/*
 ****************************************************
 *	Funcion Listado de Reservas de Cliente
 ****************************************************
 *  Parámetros
 * 	@id_cliente: 
 *	@dia: 
 *	@mes: 
 *	@anio:
 * 
 *	Devuelve las reservas realizadas por un cliente, si existen, en un arreglo.
 *  @datos_consulta_reserva: 
 *	@codigoCliente: int
 *	@dia: int
 *	@mes: int
 *	@anio: int
 * 
 *	Devuelve null si no existe.
 *
 */
function wsClienteGetReservas($id_cliente, $dia, $mes, $anio){
 
	$resultado = array(
				"ws_ok"=>false, 
				"ws_result"=>null, 
				"ws_msj"=>"", 
				"ws_error"=>"");
				
	$params = array("datos_reservas"=>array(
			"codigoCliente"=>$id_cliente,
			"dia"=>$dia,
			"mes"=>$mes,
			"anio"=>$anio));

	try{

		// Instancia Cliente
		$oSoapSClient = wsGetSoapCli();
		$resultado["ws_error"] = $oSoapSClient->getError();
		$resultado["ws_result"]	= $oSoapSClient->call('listaReservasPasajero', $params ); 
		$resultado["ws_ok"] = true;
		$resultado["ws_msj"] = "Reservas Ok!.";

	}catch (Exception $exc){
		$resultado["ws_error"] = "Excepcion en el servicio web. "  ;
		$resultado["ws_ok"] = false;
		$resultado["ws_msj"] = "No se pudo cargar su lista de reservas.";
	}

	return $resultado;
}

/*
 ****************************************************
 *	Funcion Alta de Reservas de Cliente
 ****************************************************
 *  Parámetros
 * 	@codigoCliente: int
 *	@codigoServicio: int
 *	@lugarSubida: int
 *	@lugarBajada: int
 *  @precioPasaje: float
 * 
 *	Devuelve las reservas realizadas por un cliente, si existen, en un arreglo.
 *	Devuelve un mensaje de la operacion.
 *
 */
function wsClienteAltaReserva($codCliente, $codServicio, $lugarSubida, $lugarBajada, $precioPasaje){
 
	$resultado = array(
				"ws_ok"=>false, 
				"ws_result"=>null, 
				"ws_msj"=>"", 
				"ws_error"=>""
				);
				
	$params = array("datosreserva"=>array(
			"codigoCliente"=>$codCliente,
			"codigoServicio"=>$codServicio,
			"lugarSubida"=>$lugarSubida,
			"lugarBajada"=>$lugarBajada,
			"precioPasaje"=> $precioPasaje)
			);

	try{

		// Instancia Cliente
		$oSoapSClient = wsGetSoapCli();
		$resultado["ws_error"] = $oSoapSClient->getError();
		$resultado["ws_result"]	= $oSoapSClient->call('realizarReserva', $params ); 
		$resultado["ws_ok"] = true;
		$resultado["ws_msj"] = "Reserva realizada exitosamente!";

	}catch (Exception $exc){
		$resultado["ws_error"] = "Excepcion en el servicio web. "  ;
		$resultado["ws_ok"] = false;
		$resultado["ws_msj"] = "No se pudo procesar la reserva.";
	}

	return $resultado;
}

/*
 ****************************************************
 *	Funcion Baja de Reserva de Cliente
 ****************************************************
 *  Parámetros
 * 	@codigoReserva: int 
 * 
 *	Devuelve un mensaje de la operacion.
 *
 */
function wsClienteBajaReserva($codReserva){
 
	$resultado = array(
				"ws_ok"=>false, 
				"ws_result"=>null, 
				"ws_msj"=>"", 
				"ws_error"=>""
				);
				
	$params = array("codReserva"=>$codReserva	);

	try{

		// Instancia Cliente
		$oSoapSClient = wsGetSoapCli();
		$resultado["ws_error"] = $oSoapSClient->getError();
		$resultado["ws_result"]	= $oSoapSClient->call('anularReserva', $params ); 
		$resultado["ws_ok"] = true;
		$resultado["ws_msj"] = "Reserva cancelada exitosamente!";

	}catch (Exception $exc){
		$resultado["ws_error"] = "Excepcion en el servicio web. "  ;
		$resultado["ws_ok"] = false;
		$resultado["ws_msj"] = "No se pudo cancelar la reserva.";
	}

	return $resultado;
}