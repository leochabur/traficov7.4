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
			foreach ($entrada as $ent) 
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
			}
			foreach ($salida as $sal) 
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
			}
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
	$pdf->Cell(30,5,ucwords(strtolower($str)),1, 0, 'C', false);
	$pdf->Cell(30,5,"$articulo",1, 0, 'C', false);
	return $pdf;
}
