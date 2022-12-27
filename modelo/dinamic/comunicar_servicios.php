<?php
  session_start();
  set_time_limit(0);
  error_reporting(0);


  include_once ('../../controlador/ejecutar_sql.php');
  include_once ('../dinamic/manageGpxServer.php');
  include_once ('../../modelsORM/manager.php');
  include_once ('../../controlador/bdadmin.php');

  $sql = "SELECT o.id as id, o.nombre as nombre
          FROM ordenes o
          JOIN servicios s on s.id = o.id_servicio AND s.id_estructura = o.id_estructura_servicio
          JOIN cronogramas c on c.id = s.id_cronograma AND c.id_estructura =  s.id_estructura_cronograma
          where concat(fservicio,' ',o.hsalida) between DATE_SUB(now(), INTERVAL 50 MINUTE) AND DATE_ADD(now(), INTERVAL 2 HOUR)
                and o.id_estructura = 5 and isDinamic";

  $conn = conexcion(true);

  $resultSql = ejecutarSQLPDO($sql, $conn);

 // $result = array();

  $sql = "INSERT INTO dinamic_comunicaciones_ordenes (stamp, status, mensaje) VALUES (now(), 1, 'INICIO DE COMUNICACION')";

  ejecutarSQLPDO($sql, $conn);

  while ($row = mysqli_fetch_array($resultSql))
  {
    try
    {
       $result = generateOrdenGPX(array('orden' => $row['id']), $entityManager, $row['nombre']);
       $result = json_decode($result, true);
       $sql = "INSERT INTO dinamic_comunicaciones_ordenes (id_orden, stamp, response, status, mensaje)
                                                   VALUES ($row[id], now(), '$result[response]', 1, 'SERVICIO COMUNICADO EXITOSAMENTE - ($result[status])')";
    }
    catch (Exception $e)
    {
       $sql = "INSERT INTO dinamic_comunicaciones_ordenes (id_orden, stamp, status, mensaje)
                                             VALUES ($row[id], now(), 0, '".$e->getMessage()."')";
    }

    ejecutarSQLPDO($sql, $conn);
  }

  mysqli_close($conn);

?>

