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
  $q = $entityManager->createQuery("SELECT m, nt.texto as codigo
                                    FROM MovimientoDebitoFeriado m
                                    JOIN m.novedad n
                                    JOIN n.novedadTexto nt
                                    JOIN m.ctacte c
                                    JOIN c.empleado e
                                    JOIN e.empleador emp
                                    WHERE nt.id in (15, 17) AND (emp.id = 51)")
                    ->getResult();
}
catch (Exception $e){print "error ".$e->getMessage();}

  
  $result = [];

  foreach ($q as $m)
  {
    $ctacte = $m[0]->getCtaCte()->getId();

    if (!array_key_exists($ctacte, $result))
    {
      $result[$ctacte] = [];
    }

    $fecha = $m[0]->getFecha()->format('Y-m-d');

    if (!array_key_exists($fecha, $result[$ctacte]))
    {
        $result[$ctacte][$fecha] = [];
    }

    $result[$ctacte][$fecha][] = $m;
  }


  foreach ($result as $row)
  {
      foreach ($row as $k => $c)
      {
        if (count($c) > 1)
        {
          print $k."  ";
          foreach ($c as $x)
          {
            if ($x['codigo'] == 'Franco')
            {
              $x[0]->setCompensable(true);
              $x[0]->setActivo(false);
              print "   ".$x[0]->getCtaCte()->getEmpleado()."  ".$x['codigo'];
            }
            
          }
          print "<br>";
        }
      }
  }
  $entityManager->flush();

//  print_r($result);

?>
