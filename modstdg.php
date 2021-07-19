<?php
  set_time_limit(0);


  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('./controlador/bdadmin.php');



        $conn = conexcion();

        $sql = "select s.id as idServicio,
                       ord.id as idOrden,
                       c.id as idCronograma,
                       c.nombre as Cronograma,
                       cl.id as idCliente,
                       cl.razon_social as Cliente,
                       o.ciudad as Origen, 
                       d.ciudad as Destino,
                       ord.fservicio as Fecha_Servicio,
                       u.interno as interno,
                       s.hsalida as Horario_Cabecera,
                       s.hllegada,
                       u.id as idMicro
        from (select * from ordenes where fservicio = '2020-05-18' and not suspendida and not borrada and id_servicio is not null) ord
        inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
        inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
        inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
        inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
        inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
        inner join unidades u on u.id = ord.id_micro
        where c.activo and s.activo and cl.activo and not c.vacio and c.id_estructura = 1 and (c.id in (3679, 3681, 6355, 264, 3125, 3126, 3243, 3244, 3302, 5339, 5624, 5625, 5626) or cl.id <> 13)
        order by ";

        $result = mysql_query($sql, $conn) or die(mysql_error($conn));
        $ordenes = array();
        while ($row = mysql_fetch_array($result))
        {
          $ordenes[] = array('idServicio' => $row['idServicio'],
                             'idOrden' => $row['idOrden'],
                             'idCronograma' => $row['idCronograma'],
                             'Cronograma' => $row['Cronograma'],
                              'idCliente' => $row['idCliente'],
                              'Cliente' => $row['Cliente'],
                              'Origen' => $row['Origen'],
                              'Destino' => $row['Destino'],
                              'Fecha_Servicio' => $row['Fecha_Servicio'],
                              'interno' => $row['interno'],
                              'Horario_Cabecera' => $row['Horario_Cabecera'],
                              'Horario_Llegada' => $row['hllegada']);

        }
        $payload = json_encode($ordenes);
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

        print $response;

?>

