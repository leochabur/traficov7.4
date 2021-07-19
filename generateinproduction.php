<?php
  set_time_limit(0);

  include ('./controlador/bdadmin.php');
  include ('./controlador/ejecutar_sql.php');

  $conn = conexcion(true);

  /*$sql = "SELECT s.id as idServ, o.id as idOrden, fservicio as fecha, c.id as idCrono, cl.razon_social as cliente,
                 o.nombre, o.hsalida, o.hllegada, hfinserv, km,
                 ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino,
                 id_cliente, id_estructura_cliente, origen.ciudad as origenServ, destino.ciudad as destinoSer, i_v as sentido, interno
          FROM servicios s
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
          inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
          inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
          inner join (select id_servicio, id_estructura_servicio, id, fservicio, id_micro, hsalida, hllegada, nombre
                     from ordenes
                     where fservicio between '2020-10-08' AND '2020-10-10' and not borrada and not suspendida) o ON id_servicio = s.id AND id_estructura_servicio = s.id_estructura
          left join unidades u on u.id = id_micro";*/
  $sql = "SELECT s.id as idServ, o.id as idOrden, fservicio as fecha, c.id as idCrono, cl.razon_social as cliente,
                 o.nombre, o.hsalida, o.hllegada, hfinserv, km,
                 ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino,
                 id_cliente, id_estructura_cliente, origen.ciudad as origenServ, destino.ciudad as destinoSer, i_v as sentido, interno
          FROM servicios s
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
          inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
          inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
          inner join (select id_servicio, id_estructura_servicio, id, fservicio, id_micro, hsalida, hllegada, nombre
                     from ordenes
                     where fservicio between '2021-06-16' AND '2021-06-16' and not borrada and not suspendida and id_estructura = 5) o
          ON id_servicio = s.id AND id_estructura_servicio = s.id_estructura
          left join unidades u on u.id = id_micro
where tipoServicio = 'charter'";
  $result = mysqli_query($conn, $sql);

  //$row = mysqli_fetch_array($result);   //arma un array con cuya clave es el id de todos los servicios que se deben generar automaticamente, y como valor un array cuya clave es la fecha en la cual esta generada la orden
  $serviciosGenerados = array();
  $dataOrdenes = array(); //almacena la informacion necesaria para generar las ordenes
  $id = 639; //listo para iniciar
  $fecha = '2020-09-07';
  $export = array();
  while ($row = mysqli_fetch_array($result))
  {
    $srv = $row['idServ'];
    $fecha = $row['fecha'];
    $id = $row['idOrden'];
    $export[] = array('idServicio' => $srv,
                       'idOrden' => $id,
                       'idCronograma' => $row['idCrono'],
                       'Cronograma' => $row['nombre'],
                        'idCliente' => $row['id_cliente'],
                        'Cliente' => $row['cliente'],
                        'Origen' => $row['origenServ'],
                        'Destino' => $row['destinoSer'],
                        'Fecha_Servicio' => $fecha,
                        'interno' => $row['interno'],
                        'Horario_Cabecera' => $row['hsalida'],
                        'Horario_Llegada' => $row['hllegada'],
                        'direction' => $row['sentido'],
                        'type' => 'charter'
                         );
    $id++;
  }
  mysqli_free_result($result);

//print_r($export);

 // "https://admtickets.masterbus.net/api/integrations/traffic/trips"
  //"http://paxtracker.mspivak.com/api/integrations/traffic/trips"

  $payload = json_encode($export);
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
  print_r($response);

?>

