<?php
  set_time_limit(0);

  include ('./controlador/bdadmin.php');
  include ('./controlador/ejecutar_sql.php');

  $conn = conexcion(true);
  $sql = "SELECT s.id as idServ, o.id, fservicio, c.id as idCrono, cl.razon_social as cliente,
                 c.nombre, hcitacion, hsalida, hllegada, hfinserv, km,
                 ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino,
                 id_cliente, id_estructura_cliente, origen.ciudad as origenServ, destino.ciudad as destinoSer, i_v as sentido
          FROM servicios s
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
          inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
          inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
          left join (select id_servicio, id_estructura_servicio, id, fservicio
                     from ordenes
                     where fservicio between date(now()) and DATE_ADD(date(now()), INTERVAL 15 DAY)) o ON id_servicio = s.id AND id_estructura_servicio = s.id_estructura
          where c.activo and s.activo and c.tipoServicio = 'charter'
          order by s.id";

  $result = mysqli_query($conn, $sql);

  $row = mysqli_fetch_array($result);   //arma un array con cuya clave es el id de todos los servicios que se deben generar automaticamente, y como valor un array cuya clave es la fecha en la cual esta generada la orden
  $serviciosGenerados = array();
  $dataOrdenes = array(); //almacena la informacion necesaria para generar las ordenes
  while ($row)
  {
    $srv = $row['idServ'];
    $serviciosGenerados[$srv] = array();
    $dataOrdenes[$srv] = array(0 => "'$row[nombre]'",
                               1 => "'$row[hcitacion]'",
                               2 => "'$row[hsalida]'",
                               3 => "'$row[hllegada]'",
                               4 =>"'$row[hfinserv]'",
                               5 => $row['km'],
                               6 => $srv,
                               7 => 1,
                               8 => $row['ciudades_id_origen'],
                               9 => $row['ciudades_id_estructura_origen'],
                               10 => $row['ciudades_id_destino'],
                               11 => $row['ciudades_id_estructura_destino'],
                               12 => $row['id_cliente'],
                               13 => $row['id_estructura_cliente'],
                               14 => "'$row[hcitacion]'",
                               15 => "'$row[hsalida]'",
                               16 => "'$row[hllegada]'",
                               17 =>"'$row[hfinserv]'",
                               18 => 1,
                               19 => $row['idCrono'],
                               20 => $row['cliente'],
                               21 => $row['origenServ'],
                               22 => $row['destinoSer'],
                               23 => $row['sentido']);

    while ($row && ($srv == $row['idServ']))
    {
        $serviciosGenerados[$srv][$row['fservicio']] = $row['id'];
        $row = mysqli_fetch_array($result);
    }
  }
  mysqli_free_result($result);

  $fecha = new DateTime();
  $daysOut = array(0,6);
  $campos = "nombre, 
             hcitacion, 
             hsalida, 
             hllegada, 
             hfinservicio, 
             km, 
             id_servicio, 
             id_estructura_servicio, 
             id_ciudad_origen, 
             id_estructura_ciudad_origen, 
             id_ciudad_destino, 
             id_estructura_ciudad_destino, 
             id_cliente, 
             id_estructura_cliente, 
             hcitacionreal,
             hsalidaplantareal,
             hllegadaplantareal,               
             hfinservicioreal,              
             id_estructura, 
             fservicio,
             fecha_accion";
  $export = array();
  for ($i = 0; $i < 15; $i++)
  {
    $now = new DateTime();
    $fechaSql = $fecha->format('Y-m-d');

    if (!in_array($fecha->format('w'), $daysOut)) //no es ni sabado ni domingo
    {
        foreach ($serviciosGenerados as $key => $ordenes) // recorre el array con lso servicios que se deben generar de manera automatica
        {
            if (!array_key_exists($fechaSql, $ordenes)) //si la fecha que estoy procesando existe como clave quiere decir que la orden ya se ha creado, de no ser asi procede a crearla
            {
                $auxValues = array_slice($dataOrdenes[$key], 0, 19);
                $values = implode(",", $auxValues);
                $values.=",'$fechaSql','".$now->format('Y-m-d H:i:s')."'";
                $insert = "INSERT INTO ordenes ($campos) VALUES ($values)";
                mysqli_query($conn, $insert);
                $orden = mysqli_insert_id($conn);
            }
            else
            {
                $orden = $ordenes[$fechaSql];
            }
            $export[] = array('idServicio' => $key,
                               'idOrden' => $orden,
                               'idCronograma' => $dataOrdenes[$key][19],
                               'Cronograma' => str_replace("'", "", $dataOrdenes[$key][0]),
                                'idCliente' => $dataOrdenes[$key][12],
                                'Cliente' => $dataOrdenes[$key][20],
                                'Origen' => $dataOrdenes[$key][21],
                                'Destino' => $dataOrdenes[$key][22],
                                'Fecha_Servicio' => $fechaSql,
                                'interno' => NULL,
                                'Horario_Cabecera' => str_replace("'", "", $dataOrdenes[$key][2]),
                                'Horario_Llegada' => str_replace("'", "", $dataOrdenes[$key][3]),
                                'type' => 'charter',
                                'direction' => $dataOrdenes[$key][23]
                                 );
        }
    }
    $fecha->add(new DateInterval('P1D'));
  }

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
  if (isset($json['success']))
  {
    $result = ($json['success']?1:0);
  }
  else
  {
    $result = 0;
  }
  curl_close($curl);
  $sql = "INSERT INTO estadocomunicaciones (fecha, estado, errorMessage) VALUES (now(), ".$result.", 'Informacion generada automaticamente')";
  mysqli_query($conn, $sql);
  mysqli_close($conn);
  print_r($response);
?>

