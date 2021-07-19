<?php
  set_time_limit(0);
  error_reporting(0);
  include ('./controlador/bdadmin.php');
  include ('./controlador/ejecutar_sql.php');

 // $conn = conexcion(true);
$conn = new mysqli('131.108.43.237', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');


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
        from (select * from ordenes where fservicio = '2020-11-27' and not suspendida and not borrada and id_servicio is not null) ord
        inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
        inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
        inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
        inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
        inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
        left join unidades u on u.id = ord.id_micro
        where not c.vacio and c.id_estructura = 1 and (c.id in (3679, 3681, 6355, 264, 3125, 3126, 3243, 3244, 3302, 5339, 5624, 5625, 5626) or cl.id <> 13)
          ";

  $result = mysqli_query($conn, $sql);

  $export = array();
  //'interno' => ($internos[$row['idServ']]?$internos[$row['idServ']]:null),
  while ($row = mysqli_fetch_array($result))
  {

          $export = array('idServicio' => $row['idServ'],
                             'idOrden' => $row['idOrden'],
                             'idCronograma' => $row['idCrono'],
                             'Cronograma' => utf8_encode($row['nombre']),
                              'idCliente' => $row['id_cliente'],
                              'Cliente' => $row['cliente'],
                              'Origen' => utf8_encode($row['origenServ']),
                              'Destino' => utf8_encode($row['destinoSer']),
                              'Fecha_Servicio' => $row['fservicio'],
                              'interno' => $row['interno'],
                              'Horario_Cabecera' => $row['hsalida'],
                              'Horario_Llegada' => $row['hllegada'],
                              'type' => $row['typeServ'],
                              'direction' => $row['sentido']
                               );    
      $payload = json_encode(array($export));
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
      curl_close($curl);
      print "Numero procesamiento $i - Numero orden $row[idOrden] => ".$response."<br>";
      print "</br>";
      $i++;
  }
  
  //die(print_r($export));
  mysqli_free_result($result);
  mysqli_close($conn);
  
//print_r($export);
  //print_r($export);
  //print "<br>";
  //https://admtickets.masterbus.net/api/integrations/traffic/trips
  //http://paxtracker.mspivak.com/api/integrations/traffic/trips
//  $payload = json_encode($export);

  //print_r($payload);

  $i=0;

 /* foreach ($export as $orden)
  {
    if (!$orden[])
    print_r($orden);
    print "<br><br>";
 /*     $payload = json_encode(array($orden));
    
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => "http://paxtracker.mspivak.com/api/integrations/traffic/trips",
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
      curl_close($curl);
      print "Numero procesamiento $i - Numero orden $orden[idOrden] => ".$response."<br>";*/
 // }
 // $i++;
 // }


?>

