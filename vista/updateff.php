<?php
     set_time_limit(0);
  include ('../controlador/bdadmin.php');
  include ('../controlador/ejecutar_sql.php');

  include('../modelsORM/src/MovimientoDebitoFeriado.php');
  include('../modelsORM/src/Novedad.php');
  include('../modelsORM/manager.php');
  include_once('../modelsORM/call.php');
  include_once('../modelsORM/controller.php');


  $inicio = new DateTime();
  $inicio->sub(new DateInterval('P1M'));  
  $fin = clone $inicio;
  $fin->add(new DateInterval('P1M'));

  while ($inicio <= $fin)
  {
          $q = $entityManager->createQuery("SELECT m, e.id as id, e.apellido as apellido
                                            FROM MovimientoCreditoFeriado m
                                            JOIN m.novedadTexto nt
                                            JOIN m.ctacte c
                                            JOIN c.empleado e
                                            WHERE m.fecha = :desde AND m.activo = :activo AND ((nt.id = :one) OR (nt.id = :two)) 
                                            ORDER BY e.apellido");

          $q->setParameter('desde',  $inicio->format('Y-m-d'))
            ->setParameter('one', 15)
            ->setParameter('two', 17)
            ->setParameter('activo', true);
          $movimientos = $q->getResult();
          $lastId = "";
          foreach ($movimientos as $m)
          {

            if ($m['id'] == $lastId)
            {
              if ($lasmc->getNovedadTexto()->getIsFranco())
              {
                  print "FECHA: ".$lasmc->getFecha()->format("d/m/Y")."  - Empleado: ".$lasmc['apellido'];

              }
              else
              {
                print "FECHA: ".$m[0]->getFecha()->format("d/m/Y")."  - Empleado: ".$m['apellido'];
              }
            }
            print 'uno';

              $lastId = $m['id'];
              $lasmc = $m[0];
          }




          $inicio->add(new DateInterval('P1D'));
  }
?>
