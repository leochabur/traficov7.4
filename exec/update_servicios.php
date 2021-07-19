<?php
  set_time_limit(0);


 // $conn = conexcion(true);
$conn = new mysqli('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
  $sqlInternos = "SELECT s.id as idServ, interno
          FROM servicios s
          inner join (select hsalida, hllegada, fservicio, id, id_micro, id_servicio, id_estructura_servicio
          from ordenes where fservicio = '2020-11-24') o ON o.id_servicio = s.id AND o.id_estructura_servicio = s.id_estructura
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
          inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
          inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
          left join unidades u on u.id = o.id_micro
          where c.activo and s.activo and c.tipoServicio = 'charter' and id_cliente = 10 and c.tipoServicio = 'charter'
          order by i_v ,nombre";
  $resultInternos = mysqli_query($conn, $sqlInternos);
  $internos = array();
  while ($row = mysqli_fetch_array($resultInternos))
  {
    $internos[$row['idServ']] = $row['interno'];
  }

  $desde = new DateTime();
  $desde->add(new DateInterval('P7D'));
  $hasta = clone $desde;
  $hasta->add(new DateInterval('P5D'));

  $sql = "SELECT s.id as idServ,
                       ord.id as idOrden,
                       c.id as idCrono,
                       ord.nombre as nombre,
                       cl.id as idCliente,
                       cl.razon_social as cliente,
                       o.ciudad as origenServ, 
                       d.ciudad as destinoSer,
                       ord.fservicio as fservicio,
                       u.interno as interno,
                       ord.hsalidaplantareal as Horario_Cabecera,
                       ord.hllegadaplantareal as hllegada,
                       s.i_v as direction, 
                       c.tipoServicio as typeServ,
                       cl.id as id_cliente,
                       concat(ord.fservicio,' ', ord.hsalida) as dtsalida,
                       concat(ord.fservicio,' ', ord.hcitacion) as dtcitacion,
                       concat(ord.fservicio,' ', ord.hllegada) as dtllegada,
                       ord.hsalida as hsalida,
                       ord.hllegada as hllegada,
                       s.i_v as sentido
        from (select * from ordenes where fservicio between '".$desde->format('Y-m-d')."' AND '".$hasta->format('Y-m-d')."' and not suspendida and not borrada and id_servicio is not null) ord
        inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
        inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
        inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
        inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
        inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
        left join unidades u on u.id = ord.id_micro
        where not c.vacio and c.id_estructura = 1 and (c.id in (3679, 3681, 6355, 264, 3125, 3126, 3243, 3244, 3302, 5339, 5624, 5625, 5626) or cl.id <> 13) and c.tipoServicio = 'charter'";

  $result = mysqli_query($conn, $sql);

  $export = array();
  //'interno' => ($internos[$row['idServ']]?$internos[$row['idServ']]:null),
  while ($row = mysqli_fetch_array($result))
  {

          $export[] = array('idServicio' => $row['idServ'],
                             'idOrden' => $row['idOrden'],
                             'idCronograma' => $row['idCrono'],
                             'Cronograma' => utf8_encode($row['nombre']),
                              'idCliente' => $row['id_cliente'],
                              'Cliente' => $row['cliente'],
                              'Origen' => utf8_encode($row['origenServ']),
                              'Destino' => utf8_encode($row['destinoSer']),
                              'Fecha_Servicio' => $row['fservicio'],
                              'interno' => ($row['interno']?$row['interno']:$internos[$row['idServ']]),
                              'Horario_Cabecera' => $row['hsalida'],
                              'Horario_Llegada' => $row['hllegada'],
                              'type' => $row['typeServ'],
                              'direction' => $row['sentido']
                               );    
  }
  
//  die(print_r($export));
  mysqli_free_result($result);
  
//print_r($export);
  //print_r($export);
  //print "<br>";
  //https://admtickets.masterbus.net/api/integrations/traffic/trips
  //http://paxtracker.mspivak.com/api/integrations/traffic/trips
//  $payload = json_encode($export);

  //print_r($payload);

  $i=0;
  
  $fecha = new DateTime();
  foreach ($export as $orden)
  {
      $payload = json_encode(array($orden));
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
      $result = 0;

      $json = json_decode($response, true);
      $message = '';
      if (isset($json['success']))
      {
          $result = $json['success'];
          if ($result)
            $result = 1;
          else
          {
            $result = 0;
            $mensajeError = $json['error'];
            $message = $response;
          }
          
      }
      else
      {
          $result = 0;
          $message = $response;
      }

      $insert = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) 
                VALUES ('".$fecha->format('Y-m-d H:i:s')."', $orden[idOrden], $result, '$message')"; 
      mysqli_query($conn, $insert);
      curl_close($curl);
  }

  mysqli_close($conn);
  


?>

