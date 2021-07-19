<?php
     set_time_limit(0);
  include ('../controlador/bdadmin.php');
  include ('../controlador/ejecutar_sql.php');

  include('../modelsORM/src/MovimientoDebitoFeriado.php');
  include('../modelsORM/src/Novedad.php');
  include('../modelsORM/manager.php');
  include_once('../modelsORM/call.php');
  include_once('../modelsORM/controller.php');




try
{
          $ff = $entityManager->createQuery("SELECT m
                                            FROM MovimientoDebitoFeriado m
                                            JOIN m.ctacte cc
                                            JOIN cc.empleado e
                                            JOIN m.novedad nv
                                            JOIN nv.novedadTexto nt
                                            WHERE m.activo = :activo AND nt.id in (:novedades)
                                            ORDER BY e.id, m.fecha ASC");
          $ff->setParameter('activo', true)
             ->setParameter('novedades', [15, 16]);
          $q = $ff->getResult();
}
catch (Exception $e){print "error ".$e->getMessage();}



  foreach ($q as $m)
  {
      $fecha = $m->getFecha();

      $mes = $fecha->format('m');
      $anio = $fecha->format('Y');

      if ($fecha->format('d') > 25)
      {
          $fecha->add(new DateInterval('P1M'));
          $mes = $fecha->format('m');
          $anio = $fecha->format('Y');
      }

      $m->setPeriodoAnio($anio);
      $m->setPeriodoMes($mes);
  }
  $entityManager->flush();

//  print_r($result);

?>
