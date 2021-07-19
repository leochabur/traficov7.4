<?php

set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include('../../modelsORM/manager.php'); 
include('../../controlador/bdadmin.php');
include_once('../../modelsORM/call.php'); 
include_once('../../modelsORM/controller.php'); 
include_once('../../modelsORM/src/LineaPlanilla.php'); 
include ('../../modelo/utils/dateutils.php');
include('../../PHPExcel/Classes/PHPExcel.php');
//use Symfony\Component\Validator\Validation;

$accion = $_GET['acc'];

if ($accion == 'tpdf')
{
	$planilla = find('PlanillaDiaria', $_GET['pl']);
	$cliente = $planilla->getCliente()->getId();

	$complete = $_GET['cmp'];

 	$fecha = dateToMysql($_GET['fec'], '/');
	$sql = "select s.id as servicio, o.id as orden, u.interno, apellido, 
				   time_format(if (i_v = 'i', hllegadaplantareal, hsalidaplantareal), '%H:%i') as horario, cantpax, o.nombre as nombreOrden
			from ordenes o
			inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
			inner join tiposervicio ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
			inner join cicloinforme ci on ci.id_tiposervicio = ts.id and ci.id_estructuratiposervicio = ts.id_estructura and ci.id_cliente = o.id_cliente and ci.id_estructura_cliente = o.id_estructura_cliente
			left join unidades u on u.id = o.id_micro
			left join empleados e on e.id_empleado = o.id_chofer_1
			where
			    (concat(fservicio, ' ', o.hsalida) between concat('$fecha',' ', ingresodesde) and concat('$fecha',' 23:59:59')  or
			    concat(fservicio, ' ', o.hllegada) between concat('$fecha',' ', ingresodesde) and concat('$fecha',' 23:59:59')  or
			    concat(fservicio,' ',o.hsalida) between date_add(concat('$fecha',' 00:00:00'), interval 1 day) and date_add(concat('$fecha', ' ',salidahasta), interval 1 day))
			      and o.id_estructura = $_SESSION[structure] and o.id_cliente = $cliente and not borrada and not suspendida";

	$conn = conexcion(true);
	//$conn->set_charset('utf8');
	$servicios = array();

	if ($result = mysqli_query($conn, $sql))
	{
	    while ($row = mysqli_fetch_assoc($result)) 
	    {
	    	if (!array_key_exists($row['servicio'], $servicios))
	    	{
	    		$servicios[$row['servicio']] = array();
	    	}
	    	$servicios[$row['servicio']][$row['orden']] = array('orden' => $row['nombreOrden'], 'int' => $row['interno'], 'cond' => $row['apellido'], 'hora' => $row['horario'], 'pax' => $row['cantpax']);
	    }
    }
    mysqli_free_result($result);
	mysqli_close($conn);

	$excel = new PHPExcel();
	 $excel->getProperties()->setCreator("liuggio")
	       ->setLastModifiedBy("Giulio De Donato")
	       ->setTitle("Office 2005 XLSX Test Document")
	       ->setSubject("Office 2005 XLSX Test Document")
	       ->setDescription("Test document for Office 2005 XLSX, generado usando clases de PHP")
	       ->setKeywords("office 2005 openxml php")
	       ->setCategory("Archivo de ejemplo");
$styleArray = array(
                          'borders' => array(
                            'allborders' => array(
                              'style' => 'thin'
                            )
                          )
                        );
	$excel->setActiveSheetIndex(0)
            ->mergeCells('A1:k1');
    $excel->setActiveSheetIndex(0)->setCellValue('A1', 'Certificacion servicios fecha '.$_GET['fec']);

	$i=2; 
	$salto = 49;
	$page = 0;
    $match = "/\([A-Z]\)$/";

    $nextInstance = array("A" => "B", "B" => "C", "C" => "D", "D" => "E", "E" => "F", "F" => "G", "G" => "H", "H" => "I");
	foreach ($planilla->getBloques() as $bloque) 
	{
		$excel = getHeaderExcel($excel, $bloque->getTituloEntrada(), $bloque->getTituloBloque(), $bloque->getTituloSalida(), $i);
		$i = $i + 3;
		foreach ($bloque->getLineas() as $linea) 
		{	
			$imprimio = false; //cuando no tenga ningun servicio que igual imprima la linea de la planilla

			$entrada = array();
			if ($linea->getEntrada()) //existe el servicio de entrada
			{
				$entrada = (array_key_exists($linea->getEntrada()->getId(), $servicios)?$servicios[$linea->getEntrada()->getId()]:array());
				if (count($entrada)) // existe al menos un servicio
				{
					unset($servicios[$linea->getEntrada()->getId()]);
				}
			}
			$salida = array();
			if ($linea->getSalida()) // existe el servicio de salida
			{
				$salida = (array_key_exists($linea->getSalida()->getId(), $servicios)?$servicios[$linea->getSalida()->getId()]:array());
				if (count($salida)) // existe al menos un servicio
				{
					unset($servicios[$linea->getSalida()->getId()]);
				}
			}

			foreach ($nextInstance as $k => $v) //va a recorrer todas las instancias, 
			{
				if (count($entrada) || count($salida)) //existen servicios de entrada
				{
					$imprimio = true;
					$idEntrada = existInstanceSalida($entrada, $k, $match); 
					if ($idEntrada)//existe una instancia del servicio para el ingreso
					{
						$servicioEntrada = $entrada[$idEntrada]; //recupera la orden
						unset($entrada[$idEntrada]);

						$excel = getTd($excel, $servicioEntrada, $complete, 'I', $i);
						$excel = getMedium($excel, $linea->getLocalidad(), $linea->getNombreLinea()."($k)", $linea->getArticulo(), $i);
					}

					$idSalida = existInstanceSalida($salida, $k, $match); 
					if ($idSalida)//existe una instancia del servicio para el egreso
					{
						$servicioSalida = $salida[$idSalida]; //recupera la orden
						unset($salida[$idSalida]);

						//antes de imprimir el servicio de salida, debe verificar si impimio el servicio de entrada, sino debe imprimir la fila en blanco
						if (!$idEntrada)
						{
							$excel =getTdWhite($excel, 'I', $i);	
							$excel = getMedium($excel, $linea->getLocalidad(), $linea->getNombreLinea()."($k)", $linea->getArticulo(), $i);						
						}
						$excel = getTd($excel, $servicioSalida, $complete, 'S', $i);
					}
					else{

						$excel =getTdWhite($excel, 'S', $i);
					}
					$i++;
				}
			}

		/*	foreach ($entrada as $ent) 
			{

				$imprimio = true;
				$sal = array_shift($salida);
				$excel = getTd($excel, $ent, $complete, 'I', $i);
				$excel = getMedium($excel, $linea->getLocalidad(), $linea->getNombreLinea(), $linea->getArticulo(), $i);

				if ($sal)
				{
					$excel = getTd($excel, $ent, $complete, 'S', $i);
				}
				else{
					$excel =getTdWhite($excel, 'S', $i);
				}
				$i++;			
			}
			foreach ($salida as $sal) 
			{
				$imprimio = true;
				$excel =getTdWhite($excel, 'I', $i);
				$excel = getMedium($excel, $linea->getLocalidad(), $linea->getNombreLinea(), $linea->getArticulo(), $i);
				$excel = getTd($excel, $ent, $complete, 'S', $i);
				$i++;
			}*/
			if (!$imprimio)
			{
				
				$excel =getTdWhite($excel, 'I', $i);
				$excel = getMedium($excel, $linea->getLocalidad(), $linea->getNombreLinea(), $linea->getArticulo(), $i);
				$excel =getTdWhite($excel, 'S', $i);
				$i++;
			}
		}
	}

	if (count($servicios))
	{
		$excel = getHeaderExcel($excel, '', 'Servicios sin Asignar', '', $i);
		$i = $i + 2;
		foreach ($servicios as $serv) 
		{
			foreach ($serv as $orden) 
			{
				$excel = getTd($excel, $orden, $complete, 'I', $i);
				$excel = getMedium($excel, '', "$orden[orden]", '', $i);
				$excel =getTdWhite($excel, 'S', $i);
				$i++;
			}
		}
	}
	$excel->getActiveSheet()->getStyle('A1:K'.$i)->applyFromArray($styleArray);


header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Servicios_'.$fecha.'.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
ob_get_clean();
$objWriter->save('php://output');
ob_end_flush();
}

function getHeaderExcel($excel, $entrada, $bloque, $salida, $i)
{
 
	$excel->setActiveSheetIndex(0)
			->mergeCells("A$i:K$i");
	$i++;
	$excel->setActiveSheetIndex(0)
	    ->mergeCells("A$i:D$i")
	    ->mergeCells("E$i:G$i")
	    ->mergeCells("H$i:K$i")
	   ->setCellValue("A$i", $entrada)
	   ->setCellValue("E$i", $bloque)
	   ->setCellValue("H$i", $salida);
	$i++;
	$excel->setActiveSheetIndex(0)
	   ->setCellValue("A$i", 'Interno')
	   ->setCellValue("B$i", 'Conductor')
	   ->setCellValue("C$i", 'Hora')
	   ->setCellValue("D$i", 'Pax')
	   ->setCellValue("E$i", 'Loalidad')
	   ->setCellValue("F$i", 'Recorrido')
	   ->setCellValue("G$i", 'Articulo')
	   ->setCellValue("H$i", 'Interno')
	   ->setCellValue("I$i", 'Conductor')
	   ->setCellValue("J$i", 'Hora')
	   ->setCellValue("K$i", 'Pax');
	  return $excel;
}

function getTd($excel, $td, $complete = true, $entrada, $i)
{
	$cond = ucwords(strtolower(($td['cond'])));
	$hora = "$td[hora]";
	$pax = "$td[pax]";
	if (!$complete)
	{
		$hora = "";
		$pax = "";
	}
	if ($entrada == 'I')
	{
		$excel->setActiveSheetIndex(0)
		   ->setCellValue("A$i", $td['int'])
		   ->setCellValue("B$i", $cond)
			->setCellValue("C$i", $hora)
			->setCellValue("D$i", $pax);
	}
	else{
		$excel->setActiveSheetIndex(0)
		   ->setCellValue("H$i", $td['int'])
		   ->setCellValue("I$i", $cond)
			->setCellValue("J$i", $hora)
			->setCellValue("K$i", $pax);
	}

	return $excel;
}

function getTdWhite($excel, $entrada, $i)
{
	if ($entrada == 'I')
	{
		$excel->setActiveSheetIndex(0)
		   ->setCellValue("A$i", '')
		   ->setCellValue("B$i", '')
			->setCellValue("C$i", '')
			->setCellValue("D$i", '');
	}
	else{
		$excel->setActiveSheetIndex(0)
		   ->setCellValue("H$i", '')
		   ->setCellValue("I$i", '')
			->setCellValue("J$i", '')
			->setCellValue("K$i", '');
	}
	return $excel;
}

function getMedium($excel, $localidad, $recorrido, $articulo, $i)
{
	$localidad = ucwords(strtolower(($localidad)));

	$excel->setActiveSheetIndex(0)
	   ->setCellValue("E$i", $localidad)
	   ->setCellValue("F$i", $recorrido)
	   ->setCellValue("G$i", $articulo);
	return $excel;
}

function existInstanceSalida($ordenes, $instance, $match) //para un array con servicios devuelve si existe entre los servicios uno con la instancia dada, de ser asi devuelve la key del mismo
{
	foreach ($ordenes as $key => $ord)
	{
		$cadena = $ord['orden'];
		if (preg_match($match, $cadena)) 
		{
			$instanceOrder = substr($cadena, -2, 1);
			if ($instanceOrder == $instance)
			{
				return $key;
			}
		}
		else //si hay una orden que no tiene intance, devuelve esa
		{
			return $key;
		}
	}
	return null;
}