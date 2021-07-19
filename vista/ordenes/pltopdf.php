<?php
header("Content-Type: text/html; charset=iso-8859-1 ");
set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);
session_start();
include('../../modelsORM/manager.php'); 
include('../../controlador/bdadmin.php');
include_once('../../modelsORM/call.php'); 
include_once('../../modelsORM/controller.php'); 
include_once('../../modelsORM/src/LineaPlanilla.php'); 
include ('../../modelo/utils/dateutils.php');
require('../../fpdf.php');
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

	$pdf = new FPDF('P');
	$pdf->SetRightMargin(4);
	$pdf->AliasNbPages();
	$pdf->SetFont('Times','',9);   
	$pdf->SetFillColor(196, 196, 196);    
    
    $pdf->SetAutoPageBreak(false);

	$i=0; 
	$salto = 49;
	$page = 0;
    $match = "/\([A-Z]\)$/";

    $nextInstance = array("A" => "B", "B" => "C", "C" => "D", "D" => "E", "E" => "F", "F" => "G", "G" => "H", "H" => "I");
	foreach ($planilla->getBloques() as $bloque) 
	{
		if (!($i%$salto))
		{
			if ($i)
				$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
			$pdf->AddPage();
			$pdf->cell(0, 5, 'PLANILLA CONTROL PASAJEROS      _     FECHA '.$_GET['fec'], 1,1,'C'); 
			$i++;  
			$page++;						
		}
		$pdf = getHeader($pdf, $bloque->getTituloEntrada(), $bloque->getTituloBloque(), $bloque->getTituloSalida());
		$i++; 
		//if ($i%$salto)
			//$i+=4;
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

			/////////////nueva forma
			foreach ($nextInstance as $k => $v) //va a recorrer todas las instancias, 
			{
				if (count($entrada) || count($salida)) //existen servicios de entrada
				{	
					$i++;
					$imprimio = true;
					$idEntrada = existInstanceSalida($entrada, $k, $match); 
					if ($idEntrada)//existe una instancia del servicio para el ingreso
					{
						$servicioEntrada = $entrada[$idEntrada]; //recupera la orden
						unset($entrada[$idEntrada]);

						$pdf = getTd($pdf, $servicioEntrada, $complete);
						$pdf = getMedium($pdf, $linea->getLocalidad(), $linea->getNombreLinea()."($k)", $linea->getArticulo());

						//imprime el servicio de entrada
						/*$tabla.="<tr>".getTd($servicioEntrada, $complete, $linea->getEntrada()->getId())."
									<td>".$linea->getLocalidad()."</td>
									<td>".$linea->getNombreLinea()." - ($k)</td>
									<td>".$linea->getArticulo()."</td>";*/
					}

					$idSalida = existInstanceSalida($salida, $k, $match); 
					if ($idSalida)//existe una instancia del servicio para el egreso
					{
						$servicioSalida = $salida[$idSalida]; //recupera la orden
						unset($salida[$idSalida]);

						//antes de imprimir el servicio de salida, debe verificar si impimio el servicio de entrada, sino debe imprimir la fila en blanco
						if (!$idEntrada)
						{
							$pdf =getTdWhite($pdf);
							/*$tabla.="<tr>".getTdWhite()."
										<td>".$linea->getLocalidad()."</td>
										<td>".$linea->getNombreLinea()." - ($k)</td>
										<td>".$linea->getArticulo()."</td>";*/							
						}

						$pdf = getTd($pdf, $servicioSalida, $complete);
						//$tabla.= getTd($servicioSalida, $complete, $linea->getSalida()->getId());
					}
					else
					{
						$pdf =getTdWhite($pdf); //$tabla.= getTdWhite()."</tr>";
					}
					$pdf->ln();	
					if (!($i%$salto))
					{
						$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
						$pdf->AddPage();
						$pdf->cell(0, 5, 'PLANILLA CONTROL PASAJEROS      _     FECHA '.$_GET['fec'], 1,1,'C');
						$pdf = getHeader($pdf, $bloque->getTituloEntrada(), $bloque->getTituloBloque(), $bloque->getTituloSalida());
						$i++;
						$page++;
					}
				}
			}
			///////////////////////////////////////


			/*foreach ($entrada as $ent) 
			{
				$i++;

				$imprimio = true;
				$sal = array_shift($salida);
				$pdf = getTd($pdf, $ent, $complete);
				$pdf = getMedium($pdf, $linea->getLocalidad(), $linea->getNombreLinea(), $linea->getArticulo());

				if ($sal)
				{
					$pdf = getTd($pdf, $sal, $complete);
				}
				else{
					$pdf =getTdWhite($pdf);
				}
				$pdf->ln();	
				if (!($i%$salto))
				{
					$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
					$pdf->AddPage();
					$pdf->cell(0, 5, 'PLANILLA CONTROL PASAJEROS      _     FECHA '.$_GET['fec'], 1,1,'C');
					$pdf = getHeader($pdf, $bloque->getTituloEntrada(), $bloque->getTituloBloque(), $bloque->getTituloSalida());
					$i++;
					$page++;
				}				
			}*/
			/*foreach ($salida as $sal) 
			{
				$i++;
				$imprimio = true;
				$pdf =getTdWhite($pdf);
				$pdf = getMedium($pdf, $linea->getLocalidad(), $linea->getNombreLinea(), $linea->getArticulo());
				$pdf = getTd($pdf, $sal, $complete);
				$pdf->ln();
				if (!($i%$salto))
				{
					$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
					$pdf->AddPage();
					$pdf->cell(0, 5, 'PLANILLA CONTROL PASAJEROS      _     FECHA '.$_GET['fec'], 1,1,'C');  
					$pdf = getHeader($pdf, $bloque->getTituloEntrada(), $bloque->getTituloBloque(), $bloque->getTituloSalida()); 
					$i++;
					$page++;
				}
			}*/
			if (!$imprimio)
			{
				$i++;
				$pdf =getTdWhite($pdf);
				$pdf = getMedium($pdf, $linea->getLocalidad(), $linea->getNombreLinea(), $linea->getArticulo());
				$pdf = getTdWhite($pdf);
				$pdf->ln();
				if (!($i%$salto))
				{
					$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
					$pdf->AddPage();
					$pdf->cell(0, 5, 'PLANILLA CONTROL PASAJEROS      _     FECHA '.$_GET['fec'], 1,1,'C');   
					$pdf = getHeader($pdf, $bloque->getTituloEntrada(), $bloque->getTituloBloque(), $bloque->getTituloSalida());
					$i++;
					$page++;
				}
			}
		}

	}

	if (count($servicios))
	{
		$pdf = getHeader($pdf,'','Servicios No Asignados','');
		foreach ($servicios as $serv) 
		{
			foreach ($serv as $orden) 
			{
				$i++;
				$pdf =getTd($pdf, $orden, $complete);
				$pdf = getMedium($pdf, '',"$orden[orden]",'');
				$pdf = getTdWhite($pdf);
				$pdf->ln();
				if (!($i%$salto))
				{
					$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
					$pdf->AddPage();
					$pdf->cell(0, 5, 'PLANILLA CONTROL PASAJEROS      _     FECHA '.$_GET['fec'], 1,1,'C');  
					$pdf = getHeader($pdf,'','Servicios No Asignados',''); 
					$i++;
					$page++;
				}
			}
		}
	}
	$pdf->cell(0, 5, 'Pagina '.$page, 0, 1, 'C'); 
    $pdf->Output();

}
elseif($accion == 'res')
{
	$planilla = find('PlanillaDiaria', $_GET['pl']);
	$cliente = $planilla->getCliente()->getId();


 	$fecha = dateToMysql($_GET['fec'], '/');
	$sql = "select s.id as servicio, count(*) as cant, o.nombre as orden
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
			      and o.id_estructura = $_SESSION[structure] and o.id_cliente = $cliente and not borrada and not suspendida
			GROUP BY s.id";


	$conn = conexcion(true);

	$servicios = array();

	if ($result = mysqli_query($conn, $sql))
	{
	    while ($row = mysqli_fetch_assoc($result)) 
	    {
	    	$servicios[$row['servicio']] = array('cant' => $row['cant'], 'nombre' => $row['orden']);
	    }
    }
    mysqli_free_result($result);
	mysqli_close($conn);

	$resumen = array();
	$bloques = array();
	$articulos = array();
	foreach ($planilla->getBloques() as $bloque) 
	{
		$resumen[$bloque->getId()] = array('nombre' => $bloque->getTituloBloque(), 'detalle' => array());
		foreach ($bloque->getLineas() as $linea) 
		{
			$articulos[strtoupper($linea->getArticulo()->getDescripcion())] = $linea->getArticulo();

			$entrada = ($linea->getEntrada()?$linea->getEntrada()->getId():null);
			if ($entrada)  //existe el servicio de entrada para la linea actual
			{				
				if (array_key_exists($entrada, $servicios)) //el servicio de entrada se encuentra diagramado
				{
					if (!array_key_exists(strtoupper($linea->getArticulo()->getDescripcion()), $resumen[$bloque->getId()]['detalle']))
					{
							$resumen[$bloque->getId()]['detalle'][strtoupper($linea->getArticulo()->getDescripcion())] = $servicios[$entrada]['cant'];		
					}
					else
					{
							$resumen[$bloque->getId()]['detalle'][strtoupper($linea->getArticulo()->getDescripcion())]+= $servicios[$entrada]['cant'];
					}
					unset($servicios[$entrada]);
				}			
			}

			$salida = ($linea->getSalida()?$linea->getSalida()->getId():null);
			if ($salida)  //existe el servicio de salida para la linea actual
			{				
				if (array_key_exists($salida, $servicios)) //el servicio de entrada se encuentra diagramado
				{
					if (!array_key_exists(strtoupper($linea->getArticulo()->getDescripcion()), $resumen[$bloque->getId()]['detalle']))
					{
							$resumen[$bloque->getId()]['detalle'][strtoupper($linea->getArticulo()->getDescripcion())] = $servicios[$salida]['cant'];		
					}
					else
					{
							$resumen[$bloque->getId()]['detalle'][strtoupper($linea->getArticulo()->getDescripcion())]+= $servicios[$salida]['cant'];
					}
					unset($servicios[$salida]);
				}			
			}
		}
	}

	$pdf = new FPDF('P');
	$pdf->SetRightMargin(4);
	$pdf->AliasNbPages();
	$pdf->SetFont('Times','',9);   
	$pdf->SetFillColor(196, 196, 196);    
    
    $pdf->SetAutoPageBreak(true);
    $pdf->AddPage();
    $pdf->cell(140, 5, 'Resumen Fecha '.$_GET['fec'], 1,1,'C', true);
    $pdf->cell(50, 5, 'Etiquetas de fila', 1,0,'C', true);
    $pdf->cell(30, 5, 'Suma de CtaMicro', 1,0,'C', true);
    $pdf->cell(30, 5, 'Costo Unitario', 1,0,'C', true);
    $pdf->cell(30, 5, 'Suma de Costo', 1,1,'C', true);


	foreach ($resumen as $key => $value) 
	{
		if (count($value['detalle']))
		{
		    $pdf->cell(140, 5, strtoupper($value['nombre']), 1,1,'C', true);

			ksort($value['detalle']);
			$cant = 0;
			$pesos = 0;
			foreach ($value['detalle'] as $k => $v) 
			{
				$art = $articulos[$k];
				$cant+= $v;
				$pesos+=($art->getImporte()*$v);

			    $pdf->cell(50, 5, "$k", 1,0,'L');
			    $pdf->cell(30, 5, "$v", 1,0,'R');
			    $pdf->cell(30, 5, ''.number_format($art->getImporte(),2,',',''), 1,0,'R');
			    $pdf->cell(30, 5, ''.number_format(($v*$art->getImporte()),2,',',''), 1,1,'R');
			}

		    $pdf->cell(50, 5, "TOTALES", 1,0,'L');
		    $pdf->cell(30, 5, "$cant", 1,0,'R');
		    $pdf->cell(30, 5, '', 1,0,'C');
		    $pdf->cell(30, 5, ''.number_format($pesos,2,',','.'), 1,1,'R');
		}
	}
	if (count($servicios))
	{
		$pdf->cell(140, 5, 'SERVICIOS NO ASIGNADOS', 1,1,'C', TRUE);

		foreach ($servicios as $value) 
		{
			    $pdf->cell(50, 5, "$value[nombre]", 1,0,'L');
			    $pdf->cell(30, 5, "$value[cant]", 1,0,'R');
			    $pdf->cell(30, 5, '', 1,0,'C');
			    $pdf->cell(30, 5, '', 1,1,'C');
		}
	}

	$pdf->Output();
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

function getHeader($pdf, $entrada, $titulo, $salida)
{
	$pdf->Cell(53,5,"$entrada",1, 0, 'C', True);
	$pdf->Cell(90,5,"$titulo",1, 0, 'C', True);
	$pdf->Cell(53,5,"$salida",1, 1, 'C', True);

	$pdf->Cell(10,5,"Interno",1, 0, 'C', True);
	$pdf->Cell(25,5,"Conductor",1, 0, 'C', True);
	$pdf->Cell(10,5,"Hora",1, 0, 'C', True);
	$pdf->Cell(8,5,"Pax.",1, 0, 'C', True);
	$pdf->Cell(30,5,"Localidad",1, 0, 'C', True);
	$pdf->Cell(30,5,"Recorrido",1, 0, 'C', True);
	$pdf->Cell(30,5,"Articulo",1, 0, 'C', True);
	$pdf->Cell(10,5,"Interno",1, 0, 'C', True);
	$pdf->Cell(25,5,"Conductor",1, 0, 'C', True);
	$pdf->Cell(10,5,"Hora",1, 0, 'C', True);
	$pdf->Cell(8,5,"Pax.",1, 1, 'C', True);
	return $pdf;
}

function getTd($pdf, $td, $complete = true)
{
	$pdf->Cell(10,5,"$td[int]",1, 0, 'L', false);
	$str = iconv('UTF-8', 'windows-1252', $td['cond']);
	$pdf->Cell(25,5,ucwords(strtolower($str)),1, 0, 'L', false);
	$hora = "$td[hora]";
	$pax = "$td[pax]";
	if (!$complete)
	{
		$hora = "";
		$pax = "";
	}
	$pdf->Cell(10,5,"$hora",1, 0, 'L', false);
	$pdf->Cell(8,5,"$pax",1, 0, 'R', false);
	return $pdf;
}

function getTdWhite($pdf)
{
	$pdf->Cell(10,5,"",1, 0, 'L', false);
	$pdf->Cell(25,5,"",1, 0, 'L', false);
	$pdf->Cell(10,5,"",1, 0, 'L', false);
	$pdf->Cell(8,5,"",1, 0, 'R', false);
	return $pdf;
}

function getMedium($pdf, $localidad, $recorrido, $articulo)
{
	$str = iconv('UTF-8', 'windows-1252', $localidad);
	$pdf->Cell(30,5,ucwords(strtolower($str)),1, 0, 'L', false);

	$str = iconv('UTF-8', 'windows-1252', $recorrido);
	$pdf->Cell(30,5,$str,1, 0, 'C', false);
	$pdf->Cell(30,5,"$articulo",1, 0, 'C', false);
	return $pdf;
}
