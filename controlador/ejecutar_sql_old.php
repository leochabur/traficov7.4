<?php
    @session_start();
    include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
    
    function comunicateUpdateInterno($id, $conn, $idInterno, $from = '')
    {
            comunicateInsertsWhitId($id, $conn, $from);
            /*  $interno = "NULL";
              if($idInterno)
              {
                $sqlMicro = "SELECT interno FROM unidades WHERE id = $idInterno";
                $resultMicro = mysql_query($sqlMicro, $conn);
                if ($rowMicro = mysql_fetch_array($resultMicro))
                {
                    $interno = $rowMicro['interno'];
                }
              }

              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => "https://admtickets.masterbus.net/api/integrations/traffic/trips/".$id,
                CURLOPT_CUSTOMREQUEST => "PATCH",
                CURLOPT_POSTFIELDS =>"{'trips': {'bus_id': $interno}}",
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
                  $message = "Update Orden FROM: ".$from;
              }
              else
              {
                  $result = 0;
                  $message = $response;
              }

              curl_close($curl);      
              $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES (now(), $id, $result, '$message')";
              mysql_query($sql, $conn);  */
    }

    function comunicateDelete($id, $conn, $from = '')
    {
              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => "https://admtickets.masterbus.net/api/integrations/traffic/trips/".$id,
                CURLOPT_CUSTOMREQUEST => "DELETE",
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
                  $message = "Delete ORDEN FROM: ".$from;
              }
              else
              {
                  $result = 0;
                  $message = $response;
              }
              curl_close($curl);   
              $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES (now(), $id,".$result.", '$message')";  
              mysql_query($sql, $conn); 
    }

    function comunicateInsertsWhitId($id, $conn, $from = '')
    {
                $sql = getSqlExport($id);
                $resOrdenGen = mysql_query($sql, $conn);
                $ordenes = array();
                while ($row = mysql_fetch_array($resOrdenGen))
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
                if($response === false)
                {
                  $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES (now(), $id,0, '". curl_error($curl)."')"; 
                  mysql_query($sql, $conn);
                }
                else
                {
                  $json = json_decode($response, true);
                  if (isset($json['success']))
                  {
                      $resultado = $json['success'];
                      $message = "Insert ORDEN FROM: ".$from;
                  }
                  else
                  {
                      $resultado = 0;
                      $message = $response;
                  }                   
                  $sql = "INSERT INTO estadocomunicaciones (fecha, orden, estado, errorMessage) VALUES (now(), $id,".$resultado.", '$message')"; 
                  mysql_query($sql, $conn) or die ($sql);
                }
                curl_close($curl);  
    }

    function getOpcion($opcion, $estructura, $conn = 0){
             $value = "";
             if (!$conn)
                $conn = conexcion();
             $sql = "SELECT valor FROM opciones WHERE (opcion = '$opcion') and (id_estructura = $estructura)";
             $result = mysql_query($sql, $conn);
             if ($data = mysql_fetch_array($result)){
                $value = $data['valor'];
             }
             if (!$conn)
                cerrarconexcion($conn);
             return $value;
    }
    
    function insert($tabla, $campos, $valores, $con=0){
             if ($con)
                $conn = $con;
             else
                 $conn = conexcion();
             if ($conn){
        //     begin($conn);
             $prox = prox($conn, $tabla);
             $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             $result = mysql_query($sql, $conn);
             $ok=0;
             if (!mysql_errno($conn)){
              //  storedSql($sql);
                $ok = mysql_insert_id($conn);
          //      commit($conn);
                if (!$con)
                   cerrarconexcion($conn);
                return $ok;
             }
             else{
                  $ok = 0;
                  $err= mysql_error($conn);
            //      rollback($conn);
                  if (!$con)
                     cerrarconexcion($conn);
                  throw new Exception("$err - $sql");
             }

             }
             else{
                  throw new Exception('Sin conexcion a la Base de Datos!.');
             }
    }
    
    function backup($tabla_original, $tabla_respaldo, $condicion, $con=0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
             }
             if (!$con){
                begin($conn);
             }
             $sql = "INSERT INTO $tabla_respaldo (SELECT * FROM $tabla_original WHERE ($condicion))"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             $result = mysql_query($sql, $conn);
             if (mysql_errno($conn)){
                $error = mysql_error($conn);
                cerrarconexcion($conn);
                throw new Exception($sql.' - '.$error);
             }
             $ok=0;
             if ($result){
                storedSql($sql);
                if (!$con){
                   commit($conn);
                   cerrarconexcion($conn);
                }
             }
             else{
                  $ok = 0;
                  if (!$con){
                     rollback($conn);
                     cerrarconexcion($conn);
                  }
             }
             return $ok;
    }
    
    function delete($tabla, $campos, $valores, $con=0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
             }
             if (!$con){
                begin($conn);
             }
             $campos = explode(',', $campos);
             $valores = explode(',', $valores);
             $cond = "";
             for ($i = 0; $i < count($campos); $i++){
                 if ($cond == ""){
                    $cond = "($campos[$i] = $valores[$i])";
                 }
                 else{
                      $cond.= "and ($campos[$i] = $valores[$i])";
                 }
             }

             $sql = "DELETE FROM $tabla WHERE ($cond)";
             $ok=1;
             mysql_query($sql, $conn);

             if (!mysql_errno($conn)){
                if (!$con){
                   commit($conn);
                   cerrarconexcion($conn);
                }
                return $ok;
             }
             else{
                  $error = mysql_error($conn);
                  if (!$con){
                     rollback($conn);
                     cerrarconexcion($conn);
                  }
                  throw new Exception($sql.' - '.$error);
             }

    }
    
    function insertOrReplace($tabla, $campos, $valores, $replace, $id){
             $conn = conexcion();
             begin($conn);
             if (!$id){
                $prox = prox($conn, $tabla);
                $sql = "INSERT INTO $tabla ($campos) VALUES ($prox, $valores)";
             }
             else{
                $sql = "INSERT INTO $tabla ($campos) VALUES ($id, $valores) on duplicate key update $replace"; //consulta para almacenar un registro - recupera el proximo id para poder enviar la consulta al sitio global y no crear inconsistencias
             }
             $result = mysql_query($sql, $conn);
             $ok=0;
             if ($result){
                storedSql($sql);
                $ok = mysql_insert_id($conn);
                commit($conn);
                cerrarconexcion($conn);
             }
             else{
                  $ok = 0;
                  rollback($conn);
                  cerrarconexcion($conn);
             }
             return $ok;
    }
    
    function update($tabla, $campos, $valores, $condReg, $con = 0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
                  begin($conn);
             }
             $campos = explode(',', $campos);
             $valores = explode(',', $valores);
             $sets = "";
             for ($i = 0; $i < count($campos); $i++){
                 if ($sets == ""){
                    $sets = $campos[$i]."=".$valores[$i];
                 }
                 else{
                      $sets.= ", ".$campos[$i]."=".$valores[$i];
                 }
             }
             
             $sql = "UPDATE $tabla SET $sets WHERE ($condReg)";
             mysql_query($sql, $conn);
             $ok=1;
             if (!mysql_errno($conn)){
                storedSql($sql);
                if (!$con){
                   commit($conn);
                   cerrarconexcion($conn);
                }
                return $ok;
             }
             else{
                  $error = mysql_error($conn);
                  if (!$con){
                     rollback($conn);
                     cerrarconexcion($conn);
                  }
                  throw new Exception($error.' - '.$sql);
             }
    }
    
    function prox($conn, $table){
             $sql = "select AUTO_INCREMENT as prox from information_schema.TABLES where TABLE_SCHEMA='".getBD()."' and TABLE_NAME='$table'";
             $result = mysql_query($sql, $conn) or die($sql);
             $data = mysql_fetch_array($result);
             return $data[0];
    }

    function ejecutarSQL($sql, $con=0){
             if ($con){
                $conn = $con;
             }
             else{
                  $conn = conexcion();
             }
             $result = mysql_query($sql, $conn);
             if (!mysql_errno($conn)){
                if (!$con){
                   cerrarconexcion($conn);
                }
                return $result;
             }
             else{
                  $error = mysql_error($conn);
                  if (!$con){
                     cerrarconexcion($conn);
                  }
                  throw new Exception($error." ".$sql);
             }
    }

  function getSqlExport($id, $estructura = 1)
  {
    return "select s.id as idServicio,
                               ord.id as idOrden,
                               c.id as idCronograma,
                               ord.nombre as Cronograma,
                               cl.id as idCliente,
                               cl.razon_social as Cliente,
                               o.ciudad as Origen, 
                               d.ciudad as Destino,
                               ord.fservicio as Fecha_Servicio,
                               u.interno as interno,
                               ord.hsalidaplantareal as Horario_Cabecera,
                               ord.hllegadaplantareal as hllegada
                from (select * from ordenes where id = $id and not suspendida and not borrada and id_servicio is not null) ord
                inner join servicios s on s.id = ord.id_servicio and s.id_estructura = ord.id_estructura_servicio
                inner join cronogramas c on s.id_cronograma = c.id and s.id_estructura_cronograma = c.id_estructura
                inner join ciudades o on o.id = ciudades_id_origen and o.id_estructura = ciudades_id_estructura_origen
                inner join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
                inner join clientes cl on cl.id = c.id_cliente and cl.id_estructura = c.id_estructura_cliente
                left join unidades u on u.id = ord.id_micro
                where c.activo and s.activo and cl.activo and not c.vacio and c.id_estructura = $estructura";
  }
?>
