<?php
  set_time_limit(0);

  include ('../controlador/bdadmin.php');
  include ('../modelsORM/src/MovimientoCreditoFeriado.php');
  include ('../modelsORM/src/Estructura.php');
  include ('../modelsORM/src/NovedadTexto.php');
  include ('../modelsORM/src/Empleado.php');
  include ('../modelsORM/src/CtaCteFeriado.php');
  include ('../modelsORM/src/Propietario.php');
  include ('../modelsORM/manager.php');

  include_once ('../modelsORM/call.php');
  include_once ('../modelsORM/controller.php');

  /*$conn = conexcion(true);

  $sql = "SELECT c.id as codigo, n.desde as fecha, n.id_empleado as emple, n.id as idNov, desde
          from novedades n
          join cod_novedades c on c.id = n.id_novedad
          where (isFeriado or isFranco) and n.id_estructura = 1 and desde > '2020-09-25' and n.id not in (SELECT id_novedad FROM rrhh_movimiento_debito_feriado)";

  $result = mysqli_query($conn, $sql);

  En septiembre de 2022 cambia el rango de liquidacion, del 26 al 25   pasa a liquidarse del 21 al 20

  */


  $fecha = DateTime::createFromFormat('Y-m-d', '2022-11-22'); //new DateTime();

  //$fecha = new DateTime();

  $inicio = DateTime::createFromFormat('Y-m-d', $fecha->format('Y-'.($fecha->format('m')-1).'-21'));
  $fin = DateTime::createFromFormat('Y-m-d', $fecha->format('Y-m-20'));

  
  if ($fecha->format('d') > 20)
  {
    $inicio->add(new DateInterval('P1M'));
    $fin->add(new DateInterval('P1M'));
  }

  $ctactes = array();
  $str = find('Estructura', 1);
  $empleador = getPropietarioIndividual(51); //1 Master   -   51 Sintra

  $novFranco = find('NovedadTexto', 15);
  $novFeriado = find('NovedadTexto', 17);
  //
  $empleados = getEmpleados($str, $empleador);

 // die('ok');

  $feriados = getFeriados($str, $inicio, $fin);


  foreach ($empleados as $emp)
  {
      $cc = getCtaCteFeriado($emp);
      if (!$cc)
      {
        $cc = new CtaCteFeriado(); 
        $cc->setEmpleado($emp);
        $entityManager->persist($cc);
      }

      $franco = getCreditoConTipoNovedad($novFranco, $emp, $inicio, $fin);
      if (!$franco)
      {
        $franco = new MovimientoCreditoFeriado();
        $franco->setFecha($inicio);
        $franco->setFechaCarga(new DateTime());

        $franco->setPeriodoMes($fin->format('m'));
        $franco->setPeriodoAnio($fin->format('Y'));
        $franco->setCantidad(6);
        $franco->setDescripcion($novFranco->getTexto().'s - Periodo '.$fin->format('m-Y'));
        $franco->setEstructura($str);
        $franco->setNovedadTexto($novFranco);
        $franco->setCtacte($cc);
        $entityManager->persist($franco);
      }

      foreach ($feriados as $f)
      {
          $feriado = getCreditoConFeriado($f, $emp);
          if (!$feriado)
          {
            $feriado = new MovimientoCreditoFeriado();
            $feriado->setFecha($f->getFecha());
            $feriado->setFechaCarga(new DateTime());

            $fechaFeriado = $f->getFecha();
            if ($fechaFeriado->format('d') > 20)
            {
              $fechaFeriado->add(new DateInterval('P1M'));
            }

            $feriado->setPeriodoMes($fechaFeriado->format('m'));
            $feriado->setPeriodoAnio($fechaFeriado->format('Y'));
            $feriado->setCantidad(1);
            $feriado->setDescripcion($novFeriado->getTexto().' - Fecha '.$f->getFecha()->format('d/m/Y'));
            $feriado->setEstructura($str);
            $feriado->setNovedadTexto($novFeriado);
            $feriado->setFeriadoAsociado($f);
            $feriado->setCtacte($cc);
            $entityManager->persist($feriado);
          }
      }
  }

  $entityManager->flush();


?>

