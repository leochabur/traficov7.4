<?php
  set_time_limit(0);

  $conn = new mysqli('traficonuevo.masterbus.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
  mysqli_query($conn, "SET NAMES 'utf8'");

  //mysqli_begin_transaction($conn, MYSQLI_TRANS_START_READ_WRITE);
  $feriados = getFeriados(1, $conn); //levanta todos los feriados


  /*$fechaActual = new DateTime(); 
  $fechaActual->add(new DateInterval('P2D'));

  $fechaInicio = DateTime::createFromFormat('Y-m-d', "2020-09-20"); //la fecha a partir de la cual se deben empezar a generar los servicios automaticos*/
  $ordenesAExportar = array();

  $fechaInicio = new DateTime(); 
  $fechaInicio->add(new DateInterval('P2D'));

  $fechaConsulta = clone $fechaInicio; //variable auxiliar para levantar las ordenes 
  $fechaConsulta->sub(new DateInterval('P7D'));

  $generadas = $informadas = 0;
  for ($i = 0; $i < 15; $i++)
  {
      $ordenesFechaDiagrama = array();

      $ordenesBase = array();
      if (in_array($fechaInicio->format('Y-m-d'), $feriados))
      //la fecha que debe diagramar es un feriado, debe levantar las ordenes del ultimo domingo
      {
        //corre la fecha la ultimo domingo
        $fechaAux = clone $fechaConsulta;
        $resta = $fechaAux->format('w');
        $fechaAux->sub(new DateInterval('P'.$resta.'D'));

        $ordenesBase = getOrdenesDiagramadas($fechaAux->format('Y-m-d'), 1, $conn, false); //recupera todas las ordenes llamadas base, se toma como que esas ordenes son las que deberan estar diagramadas (Para el caso son las ordenes de 14 dias atras)
      }
      else
      {
        $fechaAux = clone $fechaConsulta;
        while (in_array($fechaAux->format('Y-m-d'), $feriados))
        {
            $fechaAux->sub(new DateInterval('P7D'));
        } 
        $ordenesBase = getOrdenesDiagramadas($fechaAux->format('Y-m-d'), 1, $conn, false); //recupera todas las ordenes llamadas base, se toma como que esas ordenes son las que deberan estar diagramadas (Para el caso son las ordenes de 14 dias atras) 
      }


      $ordenesFechaDiagrama = getOrdenesDiagramadas($fechaInicio->format('Y-m-d'), 1, $conn, false); //son las ordenes del dia que debe generar las ordenes, con esto verifica si las mismas ya no han sido diagramadas


      foreach ($ordenesBase as $serv => $orden)//recorro las ordenes base y me fijo sino han sido ya diagramadas en las ordenesFechaDiagrama
      {
          if (!array_key_exists($serv, $ordenesFechaDiagrama)) //la orden no esta diagramada, debo sacar del arreglo ordenesBase los datos
          {
            $id = guardarOrden($orden, $fechaInicio->format('Y-m-d'), 1, $conn);
            $ordenesAExportar[] = getOrdenAInformar($orden, $id, $fechaInicio->format('Y-m-d'));
            $generadas++;
          }
          else
          {
            $ordenesAExportar[] = getOrdenAInformar($ordenesFechaDiagrama[$serv], null, null);
            $informadas++;
          }
      }

      $fechaInicio->add(new DateInterval('P1D'));
      $fechaConsulta->add(new DateInterval('P1D'));
  }
 // mysqli_commit($conn);

//https://admtickets.masterbus.net/api/integrations/traffic/trips
  $payload = json_encode($ordenesAExportar);
  $curl = curl_init();
  curl_setopt_array($curl, array(
    CURLOPT_URL => "https://admtickets.masterbus.net/api/integrations/traffic/trips",
    CURLOPT_CUSTOMREQUEST => "POST",
    CURLOPT_POSTFIELDS =>"{'trips':$payload}",
    CURLOPT_RETURNTRANSFER => 1, 
    CURLOPT_HTTPHEADER => array(
      "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
      "Content-Type: text/plain"
    ),
  ));

  $response = curl_exec($curl);


  $json = json_decode($response, true);
  if (isset($json['success']))
  {
      $result = $json['success'];
      $message = "Proceso ejecutado exitosamente. Total ordenes ".count($ordenesAExportar).". Ordenes Informadas: $informadas. Ordenes Generadas: $generadas";
  }
  else
  {
      $result = 0;
      $message = $response;
  }

  curl_close($curl);      
  $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES (now(), NULL, $result, '$message')";
  mysqli_query($conn, $sql); 

  mysqli_close($conn);

  function guardarOrden($orden, $fecha, $str, $conn)
  {
    $id_micro = ($orden['id_micro']?$orden['id_micro']:'NULL');
    $fercho = ($orden['id_chofer_1']?$orden['id_chofer_1']:'NULL');
    $sql = "INSERT 
            INTO 
            ordenes (id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, 
                     id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente,
                     id_micro, vacio, id_user, fecha_accion, hcitacionreal, hsalidaplantareal, hllegadaplantareal, hfinservicioreal, id_chofer_1)
            VALUES ($str, '$fecha', '$orden[nombre]', '$orden[hcitacion]', '$orden[hsalida]','$orden[hllegada]','$orden[hfinserv]', $orden[km], $orden[idServ], $str,
                    $orden[idO], $str, $orden[idD], $str, $orden[id_cliente], $str,
                    $id_micro, 0, 140, now(), '$orden[hcitacion]', '$orden[hsalida]','$orden[hllegada]','$orden[hfinserv]', $fercho)";
    mysqli_query($conn, $sql) or die(mysqli_error($conn));
    return mysqli_insert_id($conn);
  }

  function getFeriados($str, $conn)
  {
      $sql = "SELECT fecha FROM feriados WHERE id_estructura = $str AND NOT eliminado";

      $result = mysqli_query($conn, $sql);

      $feriados = array();

      while ($row = mysqli_fetch_array($result))
      {
        $feriados[] = $row['fecha'];
      }
      return $feriados;
  }


  function getOrdenAInformar($row, $id, $fecha)
  {
      return array('idServicio' => $row['idServ'],
                   'idOrden' => ($id?$id:$row['idOrden']),
                   'idCronograma' => $row['idCrono'],
                   'Cronograma' => $row['nombre'],
                    'idCliente' => $row['id_cliente'],
                    'Cliente' => $row['cliente'],
                    'Origen' => $row['origenServ'],
                    'Destino' => $row['destinoSer'],
                    'Fecha_Servicio' => ($fecha?$fecha:$row['fservicio']),
                    'interno' => $row['interno'],
                    'Horario_Cabecera' => $row['hsalida'],
                    'Horario_Llegada' => $row['hllegada'],
                    'type' => 'charter',
                    'direction' => $row['sentido']
                     );
  }
  //devuelve todas las ordenes diagramadas de charter para una fecha dada
  function getOrdenesDiagramadas($fecha, $str, $conn, $activas = true)
  {
    $all = "";
    if ($activas)
    {
      $all = " and not borrada and not suspendida";
    }
    $sql = "SELECT o.id as idOrden,
                   c.nombre,
                   hcitacion,
                   hsalida,
                   hllegada,
                   hfinserv,
                   km,
                   s.id as idServ,
                   ciudades_id_origen as idO,
                   ciudades_id_destino as idD,
                   id_cliente,
                   id_micro,
                   i_v,
                   fservicio,
                   c.id as idCrono,
                   cl.razon_social as cliente,
                   origen.ciudad as origenServ,
                   destino.ciudad as destinoSer,
                   i_v as sentido,
                   interno,
                   id_chofer_1
              FROM servicios s
              inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
              inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
              inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
              inner join (select id_servicio, id_estructura_servicio, id, id_micro, fservicio, id_chofer_1
                          from ordenes
                          where id_estructura = $str AND fservicio = '$fecha' and id_cliente = 13 $all) o ON id_servicio = s.id AND id_estructura_servicio = s.id_estructura
              inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
              left join unidades u on u.id = id_micro
              where c.tipoServicio = 'charter'";

      $result = mysqli_query($conn, $sql);
      $ordenes = array();
      while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
      {
        $ordenes[$row['idServ']] = $row;
      }
      return $ordenes;
  }
?>

