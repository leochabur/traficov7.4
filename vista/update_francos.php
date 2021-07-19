<?php
  set_time_limit(0);

  include ('../controlador/bdadmin.php');
  include ('../modelsORM/src/MovimientoDebitoFeriado.php');
  include ('../modelsORM/src/Estructura.php');
  include ('../modelsORM/src/NovedadTexto.php');
  include ('../modelsORM/src/Empleado.php');
  include ('../modelsORM/src/CtaCteFeriado.php');
  include ('../modelsORM/manager.php');

  include_once ('../modelsORM/call.php');
  include_once ('../modelsORM/controller.php');

  $conn = conexcion(true);

  $sql = "SELECT c.id as codigo, n.desde as fecha, n.id_empleado as emple, n.id as idNov, desde
          from novedades n
          join cod_novedades c on c.id = n.id_novedad
          where (isFeriado or isFranco or (id_novedad = 49)) and n.id_estructura = 1 and desde between '2020-12-26' and '2021-01-25' and n.id not in (SELECT id_novedad FROM rrhh_movimiento_debito_feriado)";

  $result = mysqli_query($conn, $sql);

  $novText = array();
  $ctactes = array();
  $emples = array();

  $str = find('Estructura', 1);
  while ($row = mysqli_fetch_array($result))
  {
      if (!array_key_exists($row['codigo'], $novText))
      {
          $codeNov = find('NovedadTexto', $row['codigo']);
          $novText[$row['codigo']] = $codeNov;
      }

      if (!array_key_exists($row['emple'], $emples))
      {
          $emples[$row['emple']] = find('Empleado', $row['emple']);
      }
      
      if (!array_key_exists($row['emple'], $ctactes))
      {
          $cc = getCtaCteFeriado($emples[$row['emple']]);
          if (!$cc)
          {
            $cc = new CtaCteFeriado(); 
            $cc->setEmpleado($emples[$row['emple']]);
            $entityManager->persist($cc);
          }
          $ctactes[$row['emple']] = $cc;
      }

      $debito = new MovimientoDebitoFeriado();
      $fecha = DateTime::createFromFormat('Y-m-d', $row['desde']);
      $fec = clone $fecha;
      $debito->setFecha($fec);
      $debito->setFechaCarga(new DateTime());

      if ($fecha->format('d') > 25)
      {
        $fecha->add(new DateInterval('P1M'));
      }

      $debito->setPeriodoMes($fecha->format('m'));
      $debito->setPeriodoAnio($fecha->format('Y'));
      $debito->setCantidad(1);
      $debito->setDescripcion($novText[$row['codigo']]->getTexto().' - '.$fec->format('d/m/Y'));
      $debito->setEstructura($str);
      $debito->setCtacte($ctactes[$row['emple']]);
      $novedad = getNovedad($row['idNov']);
      $debito->setNovedad($novedad);
      $entityManager->persist($debito);
  }
  $entityManager->flush();


?>

