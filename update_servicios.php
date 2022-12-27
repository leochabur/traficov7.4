<?php

  set_time_limit(0);


 // $conn = conexcion(true);
$conn = new mysqli('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
/*$sqlInternos = "SELECT s.id as idServ, interno
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
  }*/

  $desde = DateTime::createFromFormat('d-m-Y', '26-11-2021');// new DateTime();
  //$desde->add(new DateInterval('P7D'));
  $hasta = clone $desde;
//  $hasta->add(new DateInterval('P5D'));

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
                       s.i_v as sentido,
                       isDinamic
        from (select * from ordenes where fservicio between '2022-01-04' AND '2022-01-20' and not suspendida and not borrada and id_servicio is not null) ord
        inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
        inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
        inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
        inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
        inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
        inner join unidades u on u.id = ord.id_micro
        where not c.vacio and c.id_estructura = 5 and isDinamic";


  $result = mysqli_query($conn, $sql);

  $export = array();

  while ($row = mysqli_fetch_array($result))
  {
          $interno = $row['interno'];

          $data =  array('idServicio' => $row['idServ'],
                         'idOrden' => $row['idOrden'],
                         'idCronograma' => $row['idCrono'],
                         'Cronograma' => utf8_encode($row['nombre']),
                          'idCliente' => $row['id_cliente'],
                          'Cliente' => $row['cliente'],
                          'Origen' => utf8_encode($row['origenServ']),
                          'Destino' => utf8_encode($row['destinoSer']),
                          'Fecha_Servicio' => $row['fservicio'],
                          'interno' => $interno,
                          'Horario_Cabecera' => $row['Horario_Cabecera'],
                          'Horario_Llegada' => $row['hllegada'],
                          'type' => $row['typeServ'],
                          'direction' => $row['sentido'],
                          'is_dynamic' => ($row['isDinamic']?true:false)
                           );  
          $export[] = $data;

  }


mysqli_free_result($result);





$url = "http://paxtracker.mspivak.com/api/integrations/traffic/trips";


//$url = "https://paxtracker.masterbus.mspivak.com/api/integrations/traffic/trips";








  $i=0;
  
  $fecha = new DateTime();
  foreach ($export as $orden)
  {

      try{

          $payload = json_encode(array($orden));



          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS =>"{'trips':$payload}",
            CURLOPT_RETURNTRANSFER => 1, 
            CURLOPT_HTTPHEADER => array(
              "Authorization: Bearer d8Ypl7DMuQsHjjW/INIHxRXjiV1BSezxrmbTV8EWZvk=",
              "Content-Type: text/json"
            ),
          ));

          $response = curl_exec($curl);     

          if($response === false)
          {
              print 'Curl error: ' . curl_error($curl);
          }


          curl_close($curl);

          print $response;
        }
        catch (Exception $e){ print $e->getMessage(); }
  }

  mysqli_close($conn);
  


?>

