<?php
include ('../../../modelsORM/src/facturacion/FacturacionCliente.php');
include ('../../../modelsORM/call.php');
//include ('../../../modelsORM/src/lavadero/Unidad.php');
//use FacturacionCliente;
$accion = $_POST['accion'];
if ($accion == 'nwfc'){

	$cliente = find('Cliente', $_POST['cliente']);

	$fact = new FacturacionCliente();

	$che = ($_POST['che']?true:false);
	$tf = $_POST['tipof'];
	$mtoHE = $_POST['montohe'];	

	$fact->setTipoFacturacion($tf);
	$fact->setCalculaHExtra($che);
	$fact->setCliente($cliente);
	if ($mtoHE)
		$fact->setImporteHExtra($mtoHE);
	$entityManager->persist($fact);
	$entityManager->flush();
}
elseif($accion == 'nwtftv'){
	$facturacion = find('FacturacionCliente', $_POST['fact']);
	$tarifaServicio = new TarifaServicio();
	$tarifaServicio->setImporte(1000);
	$tarifaServicio->setNombre($_POST['nombre']);
	$tarifas = json_decode($_POST['tarifas'], true);
	foreach ($tarifas as $value) {
		
		$tipo = find('TipoVehiculo', $value[id]);

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
}
?>