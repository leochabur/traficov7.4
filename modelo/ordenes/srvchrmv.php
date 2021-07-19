<?php
  session_start();
  set_time_limit(0);

  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include_once ('../../modelsORM/manager.php');
  include_once ('../../modelsORM/call.php');
  include_once ('../../modelsORM/controller.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include_once ('../../controlador/bdadmin.php');

  $accion= $_POST['accion'];

  if($accion == 'resumen')
  {

    $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
    $fec = $fecha->format('Y-m-d');

   // die ($fecha->format('d/m/Y'));

try{
    $dql = "SELECT o
            FROM Orden o
            JOIN o.estructura e
            JOIN o.cliente cli
            JOIN o.servicio s
            JOIN s.cronograma c
            WHERE o.fservicio = :fecha AND c.tipoServicio = :tipo AND e.id = :str
            ORDER BY o.hcitacionReal";

    $result = $entityManager->createQuery($dql)
                            ->setParameter('fecha', $fec)
                            ->setParameter('tipo', 'charter')
                            ->setParameter('str', $_SESSION['structure'])
                            ->getResult();
}
catch(Exception $e){
  die($e->getTraceAsString());
}
     $tabla='<table class="table table-zebra">
                    <thead>
                    <tr>
                        <th>Fservicio</th>
                        <th>Orden</th>
                        <th>H. Citacion</th>
                        <th>H. Salida</th>
                        <th>Conductor</th>
                        <th>Interno</th>
                        <th>Estado</th>
                        <th>Responsable</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>';

     foreach ($result as $orden)
     {
        $responsable = '';
        $delete = "";
        $estado = "Activo";
        if ($orden->getBorrada() || $orden->getSuspendida())
        {
          $estado = "Eliminada/Suspendida";
          $responsable = $orden->getUsuario()->getUsername();
        }

        if (in_array($_SESSION['userid'], array(25, 33, 17, 60)))
        {
            if (!($orden->getBorrada() || $orden->getSuspendida()))
            {
              $delete = "<input type='button' value='Eliminar' class='delete' data-id='".$orden->getId()."'/>";              
            }
        }
        $tabla.="<tr>
                      <td align='center'>".$orden->getfServicio()->format('d/m/Y')."</td>
                      <td align='left'>".$orden->getNombre()."</td>
                      <td align='left'>".$orden->getHcitacionReal()->format('H:i')."</td>
                      <td align='left'>".$orden->getHsalidaPlantaReal()->format('H:i')."</td>
                      <td align='left'>".$orden->getConductor1()."</td>
                      <td align='rigth'>".$orden->getUnidad()."</td>
                      <td align='left'>$estado</td>
                      <td align='left'>$responsable</td>
                      <td>$delete</td>
                  </tr>";
     }

     $tabla.='</tbody>
              </table>';

     $script = "<script>
                      $('.delete').button().click(function(){
                                                              var btn = $(this);
                                                              if (confirm('Una vez eliminada la orden, no podra ser recuperada.Seguro eliminar?'))
                                                              {
                                                                $.post('/modelo/ordenes/srvchrmv.php',
                                                                      {accion : 'delete', orden : btn.data('id')},
                                                                      function(data)
                                                                      {
                                                                          var response = $.parseJSON(data);
                                                                          if (response.ok)
                                                                          {
                                                                            $('#cargar').trigger('click');
                                                                          }
                                                                          else
                                                                          {
                                                                            alert(response.message);
                                                                          }
                                                                       });
                                                              }
                        });
                </script>";
    print $tabla.$script;
    return;
  }
  elseif($accion == 'delete')
  {
      if (!in_array($_SESSION['userid'], array(25, 33, 17, 60)))
      {
          print json_encode(array('ok' => false, 'message' => 'No cuenta cn los privilegios para realizar la accion solicitada'));
          return;
      }
      try
      {
        $conn = conexcion();
        backup('ordenes', 'ordenes_modificadas', "id = $_POST[orden]", $conn);
        $orden = getOrden($_SESSION['structure'], $_POST['orden']);
        $usuario = find('Usuario', $_SESSION['userid']);

        $orden->setBorrada(true);
        $orden->setUsuario($usuario);
        $entityManager->flush();
        comunicateDelete($_POST['orden'], $conn, "DELETE RONDINES");
        print json_encode(array('ok' => true));

      }
      catch(Exception $e)
      {
        print json_encode(array('ok' => false, 'message' => $e->getTraceAsString()));
      }
  }
?>

