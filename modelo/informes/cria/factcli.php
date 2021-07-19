<?php
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include ('../../../modelsORM/src/FacturacionCliente.php');
include ('../../../modelsORM/src/TarifaServicio.php');
include ('../../../modelsORM/call.php');
include_once ('../../../modelsORM/controller.php');
//include ('../../../modelsORM/src/lavadero/Unidad.php');
//use FacturacionCliente;
$accion = $_POST['accion'];
if ($accion == 'nwfc'){
	try
	{
		$che = ($_POST['che']?true:false);
		$tf = $_POST['tipof'];
		$mtoHE = $_POST['montohe'];	

		if ($_POST['fact'])
		{
			$factura = $entityManager->find('FacturacionCliente', $_POST['fact']);
			$factura->setTipoFacturacion($tf);
			$factura->setCalculaHExtra($che);
			if ($mtoHE)
				$factura->setImporteHExtra($mtoHE);
		}
		else{
		    $q = $entityManager->createQuery("SELECT c 
					    					  FROM Cliente c 
					    					  JOIN c.estructura e
					    					  WHERE c.id = :cli AND e.id = :str");
		    $q->setParameter('cli', $_POST['cliente'])   
		      ->setParameter('str', $_POST['str']);              
		    $cliente =  $q->getOneOrNullResult();  	
			$fact = new FacturacionCliente();
			$fact->setTipoFacturacion($tf);
			$fact->setCalculaHExtra($che);
			$fact->setCliente($cliente);
			if ($mtoHE)
				$fact->setImporteHExtra($mtoHE);
			$entityManager->persist($fact);
		}
			$entityManager->flush();
			print json_encode(array('ok' => true));
	}
	catch (Exception $e){
		print json_encode(array('ok' => false, 'msge' => $e->getMessage()));
	}
}
elseif($accion == 'nwtftv'){
	try{
			$lxh = $_POST['lh']?true:false;
			$facturacion = find('FacturacionCliente', $_POST['fact']);
			$tarifaServicio = new TarifaServicio();
			$tarifaServicio->setCalculaXHora($lxh);
			$tarifaServicio->setImporte(1000);
			$tarifaServicio->setNombre($_POST['nombre']);
			$tarifas = json_decode($_POST['tarifas'], true);
			foreach ($tarifas as $value) {
				
				$estructura = find('Estructura', $_SESSION['structure']);
				$tipo = tipoVehiculo($estructura, $value[id]);

				$ttVehiculo = new TarifaTipoServicio();

				if ($value[articulo]){
					$articulo = find('ArticuloCliente', $value[articulo]);		
					$ttVehiculo->setArticulo($articulo);
				}
				$ttVehiculo->setTipo($tipo);
				$ttVehiculo->setImporte($value[monto]);
				$ttVehiculo->setDefecto($value['default']);
				$tarifaServicio->addTarifasTipoVehiculo($ttVehiculo);
			}

				//die($_POST['dias']);
			$dias = json_decode($_POST['dias'], true);
			//die(print_r($dias));
			foreach ($dias as $value) {
			//	die("el dia es $value[numero]");
				//if 
				$diaS = find('DiaSemana', $value[numero]);
				
				$tarifaServicio->addDiasSemana($diaS);
			}

			$cronos = json_decode($_POST['crono'], true);
			foreach ($cronos as $value) {
				$crono = find('Cronograma', $value[id]);
				$tarifaServicio->addCronograma($crono);
			}			

			$tarifaServicio->setFacturacion($facturacion);
			$facturacion->addTarifa($tarifaServicio);

			$entityManager->flush();
			print "Facturacion N: $_POST[fact]";
		}catch(Exception $e) {
						          			  $response = array('status' => false, 'message' => 'No se ha podido realizar la accion solicitada 3.0!! - '.$estructura."   ".$tipo."    ".$e->getMessage());
									          print json_encode($response);
									          exit();
	}
}
?>