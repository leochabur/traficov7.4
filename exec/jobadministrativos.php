<?php
  set_time_limit(0);

  //$conn = new mysqli('mariadb-masterbus-trafico.planisys.net', 'c0mbexpuser', 'Mb2013Exp', 'c0mbexport');
  $conn = mysqli_connect("mariadb-masterbus-trafico.planisys.net", "c0mbexpuser", "Mb2013Exp", "c0mbexport");
  mysqli_query($conn, "SET NAMES 'utf8'");



  $sqlInternos = "SELECT s.id as idServ, interno, u.id as idMicro
                  FROM servicios s
                  inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                  inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
                  inner join ordenes o on o.id_servicio = s.id and o.id_estructura_servicio = s.id_estructura
                  left join unidades u on u.id = id_micro
                  where fservicio = '2020-11-24' and c.activo and s.activo and c.tipoServicio = 'charter' and c.id_cliente = 10 and not borrada";

  $resultInternos = mysqli_query($conn, $sqlInternos);
  $internos = array();

  while ($row = mysqli_fetch_array($resultInternos))
  {
  		$internos[$row['idServ']] = array(0 => $row['interno'], 1 => $row['idMicro']);
  }

  //die(print_r($internos));


  $feriados = getFeriados(1, $conn); //levanta todos los feriados

  $fechaInicio = new DateTime();//::createFromFormat('Y-m-d', '2020-12-15'); //representa la fecha en la cual deberan inciarse los servicios
  $fechaInicio->add(new DateInterval('P7D'));

 // $fecha = new DateTime(); //representa la fecha actual

 // if ($fecha < $fechaInicio)  //como el proceso se va a ejecutar antes que comiencen los servicios hace esta verificacion
//  {
      $fecha = clone $fechaInicio;
//  }

  //recupera todas las ordenes hacia adelante a partir de la fecha
  $ordenesDiagramadas = getOrdenesDiagramadas($fecha, 1, $conn);


  //recupera todos los servicios marcados como charter que deben ser diagramados
  $serviciosADiagramar = getServiciosADiagramar($conn);
  //die(print_r($serviciosADiagramar));
  $generadas = 0;
  $informadas = 0;
  $ordenesAExportar = array();
  $id = '';
  for ($i = 0; $i < 5; $i++) //itera tantos dias como se quiera diagramar hacia adelante
  {  

      if ((!in_array($fecha->format('w'),array(0,6))) && (!in_array($fecha->format('Y-m-d'), $feriados)))
      {
          $ordenesServicio = array();
          if (array_key_exists($fecha->format('Y-m-d'), $ordenesDiagramadas))
          {
              $ordenesServicio = $ordenesDiagramadas[$fecha->format('Y-m-d')]; //todos los servicios diagramados para la fecha dada
          }
          
          foreach ($serviciosADiagramar as $servicio)
          {
              if (!in_array($servicio[0]['idServ'], $ordenesServicio))
              {
                  $id = guardarOrden($servicio, $fecha, 1, $conn, $internos);
                  $ordenesAExportar[] = getOrdenAInformar($servicio, $id, $fecha->format('Y-m-d'), $internos);
                  $generadas++;
              }
              else
              {
                $informadas++;
              }
          }
      }

      $fecha->add(new DateInterval('P1D'));
  }

  mysqli_close($conn);
  //print_r($ordenesAExportar);
  

//https://admtickets.masterbus.net/api/integrations/traffic/trips
  //http://paxtracker.mspivak.com/api/integrations/traffic/trips
/*  $payload = json_encode($ordenesAExportar);
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

  curl_close($curl);  */    
  $fecha = new DateTime(); 
  $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES ('".$fecha->format('Y-m-d H:i:s')."', NULL, 1, 'PROCESO EJECUTADO AUTOMATICAMENTE')";
  mysqli_query($conn, $sql); 
  mysqli_close($conn);

 // print ($json);

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

  //devuelve todas las ordenes diagramadas de charter a partir de una fecha dada
  function getOrdenesDiagramadas($fecha, $str, $conn)
  {
        $sqlOrdenesDiagramadas = "SELECT fservicio, s.id as srv
                                  FROM servicios s
                                  inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                                  inner join (select fservicio, id, id_servicio, id_estructura_servicio, borrada
                                              from ordenes 
                                              where fservicio >= '".$fecha->format('Y-m-d')."') o on o.id_servicio = s.id and o.id_estructura_servicio = s.id_estructura and not borrada
                                  where tipoServicio = 'charter' and id_cliente = 10";

        $result = mysqli_query($conn, $sqlOrdenesDiagramadas) or die (mysqli_error($conn)); //recupera hacia adelante todas las ordenes diagramadas como charter para no volver a diagramarlas

        $ordenesDiagramadas = array(); // array multidimensional, con clave Id Servicios, y datos las fechas en las que estan diagramados los mismos

        while ($row = mysqli_fetch_array($result))
        {
            $k = $row['fservicio'];
            if (!array_key_exists($k, $ordenesDiagramadas))
            {
                $ordenesDiagramadas[$k] = array();
            }
            $ordenesDiagramadas[$k][] = $row['srv'];
        }
        return $ordenesDiagramadas;
  }

  function guardarOrden($orden, $fecha, $str, $conn, $internos)
  {
      $orden = $orden[0];
      $sql = "INSERT 
              INTO 
              ordenes (id_estructura, 
                       fservicio, 
                       nombre, 
                       hcitacion, hsalida, hllegada, hfinservicio, km, 
                       id_servicio, id_estructura_servicio, 
                       id_ciudad_origen, id_estructura_ciudad_origen, 
                       id_ciudad_destino, id_estructura_ciudad_destino, 
                       id_cliente, id_estructura_cliente,
                       vacio, 
                       id_user, fecha_accion, 
                       hcitacionreal, hsalidaplantareal, hllegadaplantareal, hfinservicioreal, id_micro)
              VALUES ($str, 
                      '".$fecha->format('Y-m-d')."', 
                      '$orden[nombre]', 
                      '$orden[hcitacion]', '$orden[hsalida]','$orden[hllegada]','$orden[hfinserv]', $orden[km], 
                       $orden[idServ], $str,
                       $orden[idO], $str, 
                       $orden[idD], $str, 
                       $orden[id_cliente], $str, 
                       0, 
                       140, now(), 
                       '$orden[hcitacion]', '$orden[hsalida]','$orden[hllegada]','$orden[hfinserv]', ".($internos[$orden['idServ']][1]?$internos[$orden['idServ']][1]:'NULL').")";
      mysqli_query($conn, $sql) or die(mysqli_error($conn));
      return mysqli_insert_id($conn);
    }

  function getOrdenAInformar($row, $id, $fecha, $internos)
  { 
      $row = $row[0];
      return array('idServicio' => $row['idServ'],
                   'idOrden' =>  $id,
                   'idCronograma' => $row['idCrono'],
                   'Cronograma' => $row['nombre'],
                    'idCliente' => $row['id_cliente'],
                    'Cliente' => $row['cliente'],
                    'Origen' => $row['origenServ'],
                    'Destino' => $row['destinoSer'],
                    'Fecha_Servicio' => $fecha,
                    'interno' => $internos[$row['idServ']][0],
                    'Horario_Cabecera' => $row['hsalida'],
                    'Horario_Llegada' => $row['hllegada'],
                    'type' => $row['tipoServicio'],
                    'direction' => $row['sentido']
                     );
  }

  function getServiciosADiagramar($conn)
  {
        $sql = "SELECT s.id as idServ, c.id as idCrono, cl.razon_social as cliente,
                     c.nombre, s.hcitacion, s.hsalida, s.hllegada, s.hfinserv, km,
                     ciudades_id_origen, ciudades_id_estructura_origen, ciudades_id_destino, ciudades_id_estructura_destino,
                     id_cliente, id_estructura_cliente, origen.ciudad as origenServ, destino.ciudad as destinoSer, i_v as sentido, tipoServicio,
                     origen.id as idO, destino.id as idD
                FROM servicios s
                inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                inner join ciudades origen on origen.id = ciudades_id_origen and origen.id_estructura = ciudades_id_estructura_origen
                inner join ciudades destino on destino.id = ciudades_id_destino and destino.id_estructura = ciudades_id_estructura_destino
                inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
                where c.activo and s.activo and c.tipoServicio = 'charter' and id_cliente = 10";

        $result = mysqli_query($conn, $sql) or die (mysqli_error($conn)); //recupera todos los servicios que se deben diagramar

        $servicios = array(); // array multidimensional, con clave Id Servicios

        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC))
        {
            $k = $row['idServ'];
            if (!array_key_exists($k, $servicios))
            {
                $servicios[$k] = array();
            }
            $servicios[$k][] = $row;
        }
        return $servicios;
  }
?>

