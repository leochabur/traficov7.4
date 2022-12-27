<?php
  session_start();
  set_time_limit(0);
  error_reporting(0);

  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include_once ('../../modelsORM/manager.php');
  include_once ('../../modelsORM/call.php');
  include_once ('../../modelsORM/controller.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include_once ('../../controlador/bdadmin.php');
  include_once ('../../modelsORM/src/OrdenGPX.php');
  include_once ('../../modelsORM/src/ReservaPasajero.php');
  include_once ('../dinamic/manageGpxServer.php');

  $accion= $_POST['accion'];

  if($accion == 'resumen')
  {

      $fecha = DateTime::createFromFormat('d/m/Y', $_POST['fecha']);
      $fec = $fecha->format('Y-m-d');

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
      $head = '';
      if (in_array($_SESSION['userid'], array(102, 17)))
      {
          $head = "<th></th>";
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
                        '.$head.'
                    </tr>
                    </thead>
                    <tbody>';

     foreach ($result as $orden)
     {
        $responsable = '';
        $delete = $comunicar = "";
        $estado = "Activo";
        if ($orden->getBorrada() || $orden->getSuspendida())
        {
          $estado = "Eliminada/Suspendida";
          $responsable = $orden->getUsuario()->getUsername();
        }

        if (in_array($_SESSION['userid'], array(25, 33, 17, 60, 163)))
        {
            if (!($orden->getBorrada() || $orden->getSuspendida()))
            {
              $delete = "<input type='button' value='Eliminar' class='delete' data-id='".$orden->getId()."'/>";              
            }
        }
        
        if (in_array($_SESSION['userid'], array(102, 17, 60)))
        {
            $comunicar = "<td><input type='button' value='Comunicar' class='comunicate' data-id='".$orden->getId()."'/></td>";  
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
                      $comunicar
                  </tr>";
     }

     $tabla.='</tbody>
              </table>';

     $script = "<script>
                      $('.comunicate').button().click(function(){
                                                              var btn = $(this);
                                                              if (confirm('Seguro comunicar el servicio?'))
                                                              {
                                                                $.post('/modelo/ordenes/srvchrmv.php',
                                                                      {accion : 'comunicar', orden : btn.data('id')},
                                                                      function(data)
                                                                      {

                                                                          console.log(data);
                                                                          var response = $.parseJSON(data);
                                                                          if (response.ok)
                                                                          {
                                                                            window.open('/modelo/dinamic/downloadGpx.php?ord='+response.id+'&fn='+response.fn, '_blank '); 
                                                                          }
                                                                          else
                                                                          {
                                                                            alert(response.message);
                                                                          }
                                                                       });
                                                              }
                        });

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
  elseif($accion == 'comunicar')
  {

    try
    {
      $status = generateOrdenGPX($_POST, $entityManager);
      print $status;
      return;

    }
    catch (Exception $e){
                          print json_encode(array('ok' => false, 'message' => $e->getMessage()));
                          return;
    }
    /*  $urlPax = "http://paxtracker.mspivak.com/api/integrations/traffic/trips/$_POST[orden]/tickets";
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $urlPax,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_HTTPHEADER => array(
          "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
          "Content-Type: text/plain"
        ),
      ));

      $response = curl_exec($curl);       

      if (!curl_errno($curl)) 
      {
        switch ($http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE)) 
        {
          case 404:  
                  print json_encode(array('ok' => false, 'message' =>'No se encntro el servicio'));                  
                  break;

          case 200:
              $json = json_decode($response, true);

              $tickets = $json['data']['tickets']; //recupera todas las reservas
              if (!count($tickets)) //no hay tickets
              {
                print json_encode(array('ok' => false, 'message' => 'No existen pasajeros'));
                return;
              }
              else
              {
                try
                {
                        $ordenGpxOld = getOrdenGPX($_POST['orden'], $_SESSION['structure']);

                        $orden = getOrden($_SESSION['structure'], $_POST['orden']);

                        $destino = $orden->getDestino();


                        if (!($destino->getLatitud() && $destino->getLongitud()))
                        {
                            print json_encode(array('ok' => false, 'message' => 'No existen datos de posicionamiento geografico para el destino: '.$destino ));
                            return;
                        }

                        $ordenGPX = new OrdenGPX(); 
                        $ordenGPX->setOrden($orden);
                        $usuario = find('Usuario', $_SESSION['userid']);
                        $ordenGPX->setUsuario($usuario);  
                        $entityManager->persist($ordenGPX); 
                        
                        foreach ($tickets as $registro)
                        {
                            $idPaxTracker = $registro['id'];

                            $ticket = $registro['passenger'];

                            $pax = getPasajeroConDNI($ticket['dni']);
                            if (!count($pax))
                            {
                              print json_encode(array('ok' => false, 'message' => 'No se encuentra el pasajero con DNI: '.$ticket['dni'], 'ticket' => $tickets));
                              return;
                            }
                            elseif (count($pax) > 1)
                            {
                              print json_encode(array('ok' => false, 'message' => 'Se encontraron '.count($pax).' pasajeros con el DNI: '.$ticket['dni']));
                              return;
                            }
                            else
                            {
                              $pax = $pax[0];
                              if (!($pax->getLatitud() && $pax->getLongtud())) //falta cargar alguno de los campos de posicion geografica
                              {
                                print json_encode(array('ok' => false, 'message' => 'Falta algunos de los campos de ubicacion geografica para el pasajero con DNI: '.$ticket['dni']));
                                return;
                              }
                              else
                              {
                                $reserva = new ReservaPasajero();
                                $reserva->setPasajero($pax);
                                $reserva->setOrdenGPX($ordenGPX);
                                $reserva->setLatitud($pax->getLatitud());
                                $reserva->setLongtud($pax->getLongtud());
                                $reserva->setApellido($ticket['last_name']);
                                $reserva->setNombre($ticket['first_name']);
                                $reserva->setDireccion($pax->getDireccion());
                                $reserva->setCiudad($pax->getCiudad());
                                $reserva->setIdReservaPaxtracker($idPaxTracker);
                                $reserva->setDni($ticket['dni']);
                                $ordenGPX->addPasajero($reserva);
                                $entityManager->persist($reserva);
                              }

                            }
                        }

                        if ($ordenGpxOld) //si existe una orden gpx creada debe eliminarla y volverla a crear
                        {
                          $entityManager->remove($ordenGpxOld);
                        }

                        $entityManager->flush();   

                        $detalle = getGPXToserver([$ordenGPX]);
                        $filename = str_replace(' ', '_', utf8_encode($orden->getNombre())).$orden->getFservicio()->format('d_m_Y');
                        print json_encode(array('ok' => true, 'id' => $_POST['orden'], 'fn' => $filename, 'paxxx' => $response));
                        return;
                  }
                  catch (Exception $e){
                                        print json_encode(array('ok' => false, 'message' => $e->getMessage()));
                                        return;
                  }
              }
              break;
          default:
            print json_encode(array('ok' => false, 'message' =>'Error inesperado $http_code'));        ;
        }
      }                           
      
      curl_close($curl); */


  }
  elseif($accion == 'delete')
  {
      if (!in_array($_SESSION['userid'], array(25, 33, 17, 60, 163)))
      {
          print json_encode(array('ok' => false, 'message' => 'No cuenta con los privilegios para realizar la accion solicitada'));
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

        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => "http://67.207.88.180:3000/recorridos/$_POST[orden]",
          CURLOPT_CUSTOMREQUEST => "DELETE",
          CURLOPT_HTTPHEADER => array(
              "Content-Type: application/json"
          ),
          CURLOPT_RETURNTRANSFER => 1
        ));

        $response = curl_exec($curl);

        curl_close($curl); 


        $sql = "INSERT INTO dinamic_comunicaciones_ordenes (id_orden, stamp, response, status, mensaje)
                  VALUES ($_POST[orden], now(), '$response', 1, 'ELIMINAR ORDEN $_POST[orden]')";
        ejecutarSQL($sql, $conn);

        print json_encode(array('ok' => true));

      }
      catch(Exception $e)
      {
        print json_encode(array('ok' => false, 'message' => $e->getTraceAsString()));
      }
  }
?>

