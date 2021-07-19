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
//use Symfony\Component\Validator\Validation;

$accion = $_POST['accion'];

if ($accion == 'genpl')
{
	$planilla = find('PlanillaDiaria', $_POST['pl']);
	$cliente = $planilla->getCliente()->getId();

	$complete = (isset($_POST['complete'])?1:0);

 	$fecha = dateToMysql($_POST['fecha'], '/');
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
			      and o.id_estructura = $_SESSION[structure] and o.id_cliente = $cliente and not borrada and not suspendida
			ORDER BY s.id, o.nombre";

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
	    	$servicios[$row['servicio']][$row['orden']] = array('orden' => $row['nombreOrden'], 'int' => $row['interno'], 'cond' => $row['apellido'], 'hora' => $row['horario'], 'pax' => $row['cantpax'], 'idServ' => $row['servicio']);
	    }
    }
    mysqli_free_result($result);
	mysqli_close($conn);

	$newTarifa = "";                                                                             
    $tabla = "<table class='table table-zebra'>";     

    $match = "/\([A-Z]\)$/";

    $nextInstance = array("A" => "B", "B" => "C", "C" => "D", "D" => "E", "E" => "F", "F" => "G", "G" => "H", "H" => "I");

	foreach ($planilla->getBloques() as $bloque) 
	{
		$tabla.="<thead>
					<tr>
						<th colspan='4'>".$bloque->getTituloEntrada()."</th>
						<th colspan='3'>".$bloque->getTituloBloque()."</th>
						<th colspan='4'>".$bloque->getTituloSalida()."</th>
					</tr>
					<tr>
						<th>Interno</th>
						<th>Conductor</th>
						<th>Hora</th>
						<th>Pax.</th>
						<th>Localidad</th>
						<th>Recorrido</th>
						<th>Articulo</th>
						<th>Interno</th>
						<th>Conductor</th>
						<th>Hora</th>
						<th>Pax.</th>
					</tr>
				</thead>";
		foreach ($bloque->getLineas() as $linea) 
		{	
			$imprimio = false; //cuando no tenga ningun servicio que igual imprima la linea de la planilla
			$instance = "";
			$entrada = array(); //array conb todos los servicios de entrada correspondientes al servicio
			if ($linea->getEntrada()) //existe el servicio de entrada
			{
				$entrada = (array_key_exists($linea->getEntrada()->getId(), $servicios)?$servicios[$linea->getEntrada()->getId()]:array());
				if (count($entrada)) // existe al menos un servicio
				{
					unset($servicios[$linea->getEntrada()->getId()]);
				}
			}

			$salida = array(); //array conb todos los servicios de salida correspondientes al servicio
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

						//imprime el servicio de entrada
						$tabla.="<tr>".getTd($servicioEntrada, $complete, $linea->getEntrada()->getId())."
									<td>".$linea->getLocalidad()."</td>
									<td>".$linea->getNombreLinea()." - ($k)</td>
									<td>".$linea->getArticulo()."</td>";
					}

					$idSalida = existInstanceSalida($salida, $k, $match); 
					if ($idSalida)//existe una instancia del servicio para el egreso
					{
						$servicioSalida = $salida[$idSalida]; //recupera la orden
						unset($salida[$idSalida]);

						//antes de imprimir el servicio de salida, debe verificar si impimio el servicio de entrada, sino debe imprimir la fila en blanco
						if (!$idEntrada)
						{
							$tabla.="<tr>".getTdWhite()."
										<td>".$linea->getLocalidad()."</td>
										<td>".$linea->getNombreLinea()." - ($k)</td>
										<td>".$linea->getArticulo()."</td>";							
						}
						$tabla.= getTd($servicioSalida, $complete, $linea->getSalida()->getId());
					}
					else{

						$tabla.= getTdWhite()."</tr>";
					}
				}
			}


				
		/*	foreach ($entrada as $ent) //Array 
			{
				$imprimio = true;
				$sal = array_shift($salida);
				
				$cadena = $ent['orden'];

				if (preg_match($match, $cadena)) //verifica si el recorrido comienza tiene una instancia, es decir si finaliza con la expresion "([A-Z])"
				{
					//De ser verdadero, extrae la instancia
					$instance = substr($cadena, -2, 1); //recupera la instancia del servicio de entrada
				}
				else //sino existe la instancia, debe evaluar, si es el primer servicio se le asigna la letra A (Se considera que es la primer instancia, sino, debe evaluar cual es la instancia siguiente)
				{
					if (!$instance) //no ha asignado ninguna instancia aun
					{
						$instance = "A";
					}
					else
					{
						$instance = $nextInstance[$instance]; //Recupera la prxima instancia
					}
				}

				

				$tabla.="<tr>".getTd($ent, $complete)."
							<td>".$linea->getLocalidad()."</td>
							<td>".$linea->getNombreLinea()."</td>
							<td>".$linea->getArticulo()."</td>";
				if ($sal)
				{
					$tabla.= getTd($sal, $complete);
				}
				else{
					$tabla.=getTdWhite();
				}					
				$tabla.="</tr>";
			}*/
			/*foreach ($salida as $sal) 
			{
				$imprimio = true;
				$tabla.="<tr>".getTdWhite()."
							<td>".$linea->getLocalidad()."</td>
							<td>".$linea->getNombreLinea()."</td>
							<td>".$linea->getArticulo()."</td>";
				$tabla.= getTd($sal, $complete);				
				$tabla.="</tr>";
			}*/
			if (!$imprimio)
			{
				$tabla.="<tr>".getTdWhite()."
							<td>".$linea->getLocalidad()."</td>
							<td>".$linea->getNombreLinea()."</td>
							<td>".$linea->getArticulo()."</td>".getTdWhite()."</tr>";
			}
		}
	}

	if (count($servicios))
	{
		$tabla.="<thead>
					<tr>
						<th colspan='4'></th>
						<th colspan='3'>Servicios No Asignados</th>
						<th colspan='4'></th>
					</tr>
					<tr>
						<th>Interno</th>
						<th>Conductor</th>
						<th>Hora</th>
						<th>Pax.</th>
						<th>Localidad</th>
						<th>Recorrido</th>
						<th>Articulo</th>
						<th>Interno</th>
						<th>Conductor</th>
						<th>Hora</th>
						<th>Pax.</th>
					</tr>
				</thead>";
		foreach ($servicios as $serv) 
		{
			foreach ($serv as $orden) 
			{
				$tabla.="<tr>".getTd($orden, $complete, $orden['idServ']).getTdCenter($orden['orden']).getTdWhite()."</tr>";
			}
		}
	}
	$tabla.="</table>
			<a href='/vista/ordenes/pltopdf.php?acc=tpdf&pl=$_POST[pl]&fec=$_POST[fecha]&cmp=$complete' id='pdf' target='_blank'>Imprimir Planilla</a>
			<a href='/vista/ordenes/pltoexcel.php?acc=tpdf&pl=$_POST[pl]&fec=$_POST[fecha]&cmp=$complete' id='excel' target='_blank'>Exportar a Excel</a>
			<script>
				$('#pdf, #excel').button();
			</script>";

    print $tabla;


}
elseif ($accion == 'resumen')
{

	$planilla = find('PlanillaDiaria', $_POST['pl']);
	$cliente = $planilla->getCliente()->getId();

	$complete = (isset($_POST['complete'])?1:0);

 	$fecha = dateToMysql($_POST['fecha'], '/');
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
	//die($sql);

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
	//die(print_r($servicios));

    $tabla = "<table class='table table-zebra'>
    			<thead>
					<tr>
						<th>Etiquetas de fila</th>
						<th>Suma de CtaMicro</th>
						<th>Costo Unitario</th>
						<th>Suma de Costo</th>
					</tr>
				</thead>";
	foreach ($resumen as $key => $value) {
		if (count($value['detalle']))
		{
			$tabla.="<thead>
						<tr>
							<th>$value[nombre]</th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</thead>
					<tbody>";
			ksort($value['detalle']);
			$cant = 0;
			$pesos = 0;
			foreach ($value['detalle'] as $k => $v) {
				$art = $articulos[$k];
				$cant+= $v;
				$pesos+=($art->getImporte()*$v);
				$tabla.="<tr>
							<td>$k</td>
							<td align='right'>$v</td>
							<td align='right'>".number_format($art->getImporte(),2,',','')."</td>
							<td align='right'>".number_format(($v*$art->getImporte()),2,',','')."</td>
						 </tr>";
			}
			$tabla.="<tr>
						<td>TOTALES</td>
						<td align='right'>$cant</td>
						<td></td>
						<td align='right'>".number_format($pesos,2,',','.')."</td>
						</tbody>";
		}
	}
	if (count($servicios))
	{
		$tabla.="<thead>
					<tr>
						<th colspan='4'>Servicios No Asignados</th>
					</tr>
				</thead>
				<tbody>";
		foreach ($servicios as $value) 
		{
				$tabla.="<tr>
							<td>$value[nombre]</td>
							<td align='right'>$value[cant]</td>
							<td align='right'></td>
							<td align='right'></td>
						 </tr>";
		}
		$tabla.="<tr>
					<td></td>
					<td align='right'></td>
					<td></td>
					<td align='right'></td>
					</tbody>";
	}

	$tabla.="</table>
			<a href='/vista/ordenes/pltopdf.php?acc=res&pl=$_POST[pl]&fec=$_POST[fecha]' id='pdf' target='_blank'>Imprimir Planilla</a>
			<a href='/vista/ordenes/pltoexcel.php?acc=rxls&pl=$_POST[pl]&fec=$_POST[fecha]' id='excel' target='_blank'>Exportar a Excel</a>
			<script>
				$('#pdf, #excel').button();
			</script>";
	print $tabla;

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

function getTd($td, $complete, $code)
{
	if (!$complete)
	{
		return "<td>$td[int]</td>
				<td>$td[cond] ($code)</td>
				<td></td>
				<td></td>";
	}

	return "<td>$td[int]</td>
			<td>$td[cond] ($code)</td>
			<td>$td[hora]</td>
			<td>$td[pax]</td>";
}

function getTdWhite()
{
	return "<td></td>
			<td></td>
			<td></td>
			<td></td>";
}

function getTdCenter($orden)
{
	return "<td></td>
			<td>$orden</td>
			<td></td>";
}
