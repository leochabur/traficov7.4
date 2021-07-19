<?php
  set_time_limit(0);

  include ('./controlador/bdadmin.php');
  include ('./controlador/ejecutar_sql.php');

  $conn = conexcion(true);
  /*$sql = "SELECT s.id as idServ, o.id as idOrden, o.fservicio as fecha, c.id as idCrono, cl.razon_social as cliente,
                 c.nombre, o.hsalida, o.hllegada, o.km,
                 ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino,
                 o.id_cliente, o.id_estructura_cliente, origen.ciudad as origenServ, destino.ciudad as destinoSer, i_v as sentido
          FROM ordenes o
          INNER join servicios s ON s.id = o.id_servicio
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
          inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
          inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
          where c.tipoServicio = 'charter' and fservicio between '2020-08-05' and '2020-08-06'";*/

          $sql = "SELECT s.id as idServ, c.id as idCrono, cl.razon_social as cliente,
                 c.nombre, s.hsalida, s.hllegada, hfinserv, km,
                 ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino,
                 id_cliente, id_estructura_cliente, origen.ciudad as origenServ, destino.ciudad as destinoSer, i_v as sentido
          FROM servicios s
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
          inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
          inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
          where c.activo and s.activo and c.tipoServicio = 'charter' and id_cliente = 10
          order by s.id";

  $result = mysqli_query($conn, $sql);

  //$row = mysqli_fetch_array($result);   //arma un array con cuya clave es el id de todos los servicios que se deben generar automaticamente, y como valor un array cuya clave es la fecha en la cual esta generada la orden
  $serviciosGenerados = array();
  $dataOrdenes = array(); //almacena la informacion necesaria para generar las ordenes
  $id = 5080; //listo para iniciar

  $export = array();
  while ($row = mysqli_fetch_array($result))
  {
    $fecha = DateTime::createFromFormat('Y-m-d', "2020-11-16"); //se generaron ordenes hasta el 20/11 inclusive

    for ($i = 0; $i < 1; $i++)
    {
      if (!in_array($fecha->format('w'),array(0,6)))
      {
         // print $fecha->format('Y-m-d')."<br>";
          $export[] = array('idServicio' => $row['idServ'],
                             'idOrden' => $id,
                             'idCronograma' => $row['idCrono'],
                             'Cronograma' => $row['nombre'],
                              'idCliente' => $row['id_cliente'],
                              'Cliente' => $row['cliente'],
                              'Origen' => $row['origenServ'],
                              'Destino' => $row['destinoSer'],
                              'Fecha_Servicio' => $fecha->format('Y-m-d'),
                              'interno' => 999,
                              'Horario_Cabecera' => $row['hsalida'],
                              'Horario_Llegada' => $row['hllegada'],
                              'type' => 'charter',
                              'direction' => $row['sentido']
                               );
          $id++;          
      }
      $fecha->add(new DateInterval('P1D'));
    }
  }
  mysqli_free_result($result);
  

  print_r($export);
  print "<br>";
  //https://admtickets.masterbus.net/api/integrations/traffic/trips
  //http://paxtracker.mspivak.com/api/integrations/traffic/trips
  $payload = json_encode($export);
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
  print_r($response);
  print "<br> LAST ID : $id";
 // print_r($export);

?>

