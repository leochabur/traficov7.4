<?php

  @session_start();
  include_once ($_SERVER['DOCUMENT_ROOT'].'/modelsORM/manager.php');
  include_once ($_SERVER['DOCUMENT_ROOT'].'/modelsORM/src/MovimientoDebitoFeriado.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/call.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/modelsORM/controller.php');


function eliminarFranco($conductor, $fecha)
{
	$entityManager = $GLOBALS['entityManager'];
	try
	{
	     $empleado = find('Empleado', $conductor);
	     $cc = getCtaCteFeriado($empleado);
		$franco = getDebitoConNovedad(15, $cc, $fecha->format('Y-m-d'));
		if ($franco) //existe un Franco Sobre Feriado Diagramado Debe eliminarlo
		{
			$franco->setActivo(false);
			$franco->setCompensable(false);
			$franco->setCompensado(false);
		    $entityManager->flush();
		    return true;
		}
	}
	catch(Exception $e)
	{
		throw $e;
	}
}

function diagramarFranco($conductor, $fecha, $idNovedad, $str)
{
	$entityManager = $GLOBALS['entityManager'];
	try
	{
	     $novText = find('NovedadTexto', 15);
	     $empleado = find('Empleado', $conductor);
	     $cc = getCtaCteFeriado($empleado);

		//verificar si existe un feriado diagramado

		$feriadoDiagramado = getDebitoConNovedad(17, $cc, $fecha->format('Y-m-d'), 18);

		$debito = new MovimientoDebitoFeriado(); 

		if ($feriadoDiagramado) //existe un feriado diagramado para la fecha dada
		{
			//debo buscar si existe un franco diagramado para la fecha dada
			$francoSFeriado = getDebitoConNovedad(15, $cc, $fecha->format('Y-m-d'), 16);

			if ($francoSFeriado) //existe un Franco Sobre Feriado Diagramado Debe eliminarlo
			{
				$francoSFeriado->setActivo(false);
				$francoSFeriado->setCompensable(false);
			}

			$debito->setCompensable(true);
		}
		else //No hay feriado diagramado Debe verificar si existe Franco ya diagramado
		{
			$francoDiagramado = getDebitoConNovedad(15, $cc, $fecha->format('Y-m-d'), 16);

			if ($francoDiagramado)
			{
				$francoDiagramado->setActivo(false);
				$francoDiagramado->setCompensable(false);
			}
		}

		$estructura = find('Estructura', $str);
	    $fec = $fecha;

	    $debito->setFecha($fec);
	    $debito->setFechaCarga(new DateTime());
	    $novedad = getNovedad($idNovedad);

	    if ($fec->format('d') > 25)
	    {
	      $fec->add(new DateInterval('P1M'));
	    }

	    $debito->setPeriodoMes($fec->format('m'));
	    $debito->setPeriodoAnio($fec->format('Y'));
	    $debito->setCantidad(1);
	    $debito->setDescripcion($novText->getTexto().' - '.$fec->format('d/m/Y'));
	    $debito->setEstructura($estructura);
	    $debito->setCtacte($cc);
	    $debito->setNovedad($novedad);
	    $entityManager->persist($debito);
	    $entityManager->flush();
	    return true;
	}
	catch (Exception $e)
	{
		throw $e;
	}

}

/*

	    $novedad = new Novedad($idNovedad);
	    $novedad->setEstructura($estructura);
	    $novedad->setEmpleado($empleado);
	    $novedad->setDesde($fecha);
	    $novedad->setHasta($fecha);
	    $novedad->setNovedadTexto($novText);
	    $novedad->setEstado('no_disp');
	    $novedad->setActiva(true);
	    $novedad->setPendiente(false);
	    $novedad->setUsuario($usuario);
	    $novedad->setFechaAlta(new DateTime());
	    $novedad->setUsertxt('');
	    $entityManager->persist($novedad);*/

?>