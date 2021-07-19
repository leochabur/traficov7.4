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
  $q = $entityManager->createQuery("SELECT m
                                    FROM MovimientoDebitoFeriado m
                                    join m.ctacte cc
                                    join m.novedad n
                                    JOIN n.novedadTexto nt
                                    JOIN cc.empleado e
                                    JOIN e.empleador emp
                                    WHERE nt.id in (49) AND (emp.id = 1) AND n.activa = :activa")
                    ->setParameter('activa', true)
                    ->setParameter('fecha', '2021-07-10')
                    ->getResult();
}
catch (Exception $e){print "error ".$e->getMessage();}


$fcomp = find('NovedadTexto', 25);

  
  $result = [];
print "<table>";
  foreach ($q as $m)
  {

          $ff = $entityManager->createQuery("SELECT m
                                            FROM MovimientoDebitoFeriado m
                                            JOIN m.ctacte cc
                                            JOIN cc.empleado e
                                            JOIN m.novedad nv
                                            JOIN nv.novedadTexto nt
                                            WHERE m.activo = :activo AND nt.id in (:novedades) AND cc = :cta AND m.fecha = :fecha AND m.compensado = :comp
                                            ORDER BY e.id, m.fecha ASC");
          $ff->setParameter('activo', true)
              ->setParameter('fecha', '2021-06-20')
              ->setParameter('comp', false)
              ->setParameter('cta', $m->getCtaCte())
             ->setParameter('novedades', [15]);
          $f20 = $ff->getOneOrNullResult();

      $m->setCompensado(true)
        ->setCompensable(false)
        ->setAplicado(true);

      $m->getNovedad()->setNovedadTexto($fcomp);      

      $f20->setCompensado(true)
          ->setCompensable(true)
          ->setAplicado(true);

      $m->setDebitoOrigen($f20);
      $m->setDescripcion('Compensa Franco de fecha 20/06/2021');

      $entityManager->flush();


      print "<tr>
                <td>".$m->getCtaCte()->getEmpleado()."</td>
              </tr>";
    //  $entityManager->flush();
  }

  

//  print_r($result);

?>
