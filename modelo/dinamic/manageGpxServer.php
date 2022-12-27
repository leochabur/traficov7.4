<?php

  session_start();
  set_time_limit(0);
  error_reporting(0);

  include_once ('../../modelsORM/manager.php');
  include_once ('../../modelsORM/call.php');
  include_once ('../../modelsORM/controller.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include_once ('../../controlador/bdadmin.php');
  include_once ('../../modelsORM/src/OrdenGPX.php');
  include_once ('../../modelsORM/src/ReservaPasajero.php');
  include_once ('../enviomail/sendmail.php');

  function generateOrdenGPX($POST, $entityManager, $name = '')
  {
      $mails = 'leochabur@gmail.com,ebernues@masterbus.net,icarvajal@masterbus.net,rscalise@masterbus.net,ldominguez@masterbus.net,pfillopski@masterbus.net';
      //$urlPax = "http://paxtracker.mspivak.com/api/integrations/traffic/trips/$POST[orden]/tickets";
        $urlPax = "https://admtickets.masterbus.net/api/integrations/traffic/trips/$POST[orden]/tickets";

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
                    throw new Exception('No se encontro el servicio');
                    return;
          case 200:
              $json = json_decode($response, true);

              $tickets = $json['data']['tickets']; //recupera todas las reservas

              if (!count($tickets)) //no hay tickets
              {
                throw new Exception('No existen pasajeros');
                return;
              }
              else
              {
                try
                {
                        $ordenGpxOld = getOrdenGPX($POST['orden'], 5);

                        if ($ordenGpxOld) //si existe una orden gpx creada debe eliminarla y volverla a crear
                        {
                          $entityManager->remove($ordenGpxOld);
                        }
                        
                        $orden = getOrden(5, $POST['orden']);

                        $destino = $orden->getDestino();


                        if (!($destino->getLatitud() && $destino->getLongitud()))
                        {
                          curl_close($curl); 
                          throw new Exception('No existen datos de posicionamiento geografico para el destino: '.$destino);
                          return;
                        }

                        $ordenGPX = new OrdenGPX(); 
                        $ordenGPX->setOrden($orden);
                       // $usuario = find('Usuario', $_SESSION['userid']);
                       // $ordenGPX->setUsuario($usuario);  
                        $entityManager->persist($ordenGPX); 
                        
                        foreach ($tickets as $registro)
                        {
                            $idPaxTracker = $registro['id'];

                            $ticket = $registro['passenger'];

                            $pax = getPasajeroConDNI($ticket['dni']);
                            if (!count($pax))
                            {
                                curl_close($curl); 
                                enviarMail($mails, 'No se encuentra el pasajero con DNI: '.$ticket['dni'] , 'Error al procesar servicio '.$name);
                                throw new Exception('No se encuentra el pasajero con DNI: '.$ticket['dni']);
                                return;
                            }
                            elseif (count($pax) > 1)
                            {
                                curl_close($curl); 
                                enviarMail($mails, 'Se encontraron '.count($pax).' pasajeros con el DNI: '.$ticket['dni'] , 'Error al procesar servicio '.$name);
                                throw new Exception('Se encontraron '.count($pax).' pasajeros con el DNI: '.$ticket['dni']);
                                return;                              
                            }
                            else
                            {
                              $pax = $pax[0];
                              if (!($pax->getLatitud() && $pax->getLongtud())) //falta cargar alguno de los campos de posicion geografica
                              {
                                curl_close($curl); 
                                throw new Exception('Falta algunos de los campos de ubicacion geografica para el pasajero con DNI: '.$ticket['dni']);
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



                        $entityManager->flush();   

                        $detalle = getGPXToserver([$ordenGPX]);
                        $status = $detalle[1];
                        $filename = str_replace(' ', '_', utf8_encode($orden->getNombre())).$orden->getFservicio()->format('d_m_Y');
                        curl_close($curl); 
                        return json_encode(array('ok' => true, 'id' => $POST['orden'], 'fn' => $filename, 'response' => $response, 'status' => $status));
                  }
                  catch (Exception $e)
                  {       
                                        curl_close($curl); 
                                        throw $e;
                                        return;
                  }
              }
              break;
          default:
            curl_close($curl); 
            throw new Exception('Error inesperado $http_code');
        }
      }                           
  }



  function getGPXToserver($servicios)
  {
    $array = array();

    foreach ($servicios as $serv)
    {
        $obj = getJsonObjectToSender($serv);
        $status = $obj['status'];
        $array[] = $obj;

    }

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "http://67.207.88.180:3000/recorridos",
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($array),
        CURLOPT_HTTPHEADER => array(
          "Content-Type: application/json"
        ),
      CURLOPT_RETURNTRANSFER => 1
    ));

    $response = curl_exec($curl);

     curl_close($curl); 

     return [0 => $array, 1 => $status];//json_encode($response);  
  }




  function getJsonObjectToSender($ordengpx, $estructura = 5)
  {
  	$orden = $ordengpx->getOrden();

    $llegada = $orden->getHllegada()->format('H:i:s');

    $status ="Diagrama Sin finalizar - $llegada";

    $sql = "SELECT * 
            FROM estadoDiagramasDiarios 
            WHERE id_estado = 1  AND
                  fecha = '".$orden->getFservicio()->format('Y-m-d')."' AND
                  id_estructura = $estructura";

    $result = ejecutarSQL($sql);

    if (mysql_num_rows($result))
    {
        $llegada = $orden->getHllegadaPlantaReal()->format('H:i:s');
        $status ="Diagrama Finalizado - $llegada";
    }

  	$destino = $orden->getDestino();
    $id = $orden->getId();
    $interno = $orden->getUnidad()->getInterno();
  	$service = array(
  						"idServicio" => "$id", 
  						"nombreServicio" => $orden->getNombre(),
  						"cliente" => $orden->getCliente()->getRazonSocial(),
  						"interno" => "$interno",
  						"fechaServicio" => $orden->getFservicio()->format('Y-m-d'),
  						"horaLlegada" => $llegada,
  						"destino" => array(
  											"direccion" => $destino->getCiudad(),
  											"latitud" => $destino->getLatitud(),
  											"longitud" => $destino->getLongitud()
  										),
  						"pasajeros" => array()
  					);

  	foreach ($ordengpx->getPasajeros() as $pax)
  	{
  		$service['pasajeros'][] = array(
                                        "dni" => $pax->getDni(),
                                        "nombreApellido" => $pax.'',
                                        "lugarSubida" => array(
                                                                  "direccion" => $pax->getDireccion(),
                                                                  "latitud" => $pax->getLatitud(),
                                                                  "longitud" => $pax->getLongtud()
                                                              )
                                     );
  	}

    return $service;

  }

?>