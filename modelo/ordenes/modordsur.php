<?php
  @session_start();
  error_reporting(0);
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/enviomail/sendordbjafn.php');


 /* function getDateTimeFormat($fecha){
    $fecha = DateTime::createFromFormat('d/m/Y H:i', "$fecha");
    return $fecha->format('Y-m-d H:i');

    $fecha = date_create_from_format('j-M-Y', '15-Feb-2009');
    echo date_format($fecha, 'Y-m-d');    
  }*/

  function getCamposOren($concat, $invertir = false)
  {
        if ($concat)
        {
            if ($invertir)
            {
                return "id_estructura, fservicio, concat(nombre, '$concat'), hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_destino, id_estructura_ciudad_destino, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, id_micro, vacio, id_user, fecha_accion, cantpax, suspendida, checkeada, id_claseservicio, id_estructuraclaseservicio, peajes, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal";
            }

            return "id_estructura, fservicio, concat(nombre, '$concat'), hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, id_micro, vacio, id_user, fecha_accion, cantpax, suspendida, checkeada, id_claseservicio, id_estructuraclaseservicio, peajes, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal";
        }

        if ($invertir)
        {
            return "id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_destino, id_estructura_ciudad_destino, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, id_micro, vacio, id_user, fecha_accion, cantpax, suspendida, checkeada, id_claseservicio, id_estructuraclaseservicio, peajes, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal";
        }

        return "id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, id_micro, vacio, id_user, fecha_accion, cantpax, suspendida, checkeada, id_claseservicio, id_estructuraclaseservicio, peajes, hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal";
  }

  function getCamposHorariosOrdenesSur()
  {
    return "id_orden, citacion, salida, llegada, finalizacion, id_estructura_orden, citacion_real, salida_real, llegada_real, finalizacion_real, cod_servicio";
  }

  function executeSql($conn, $sql)
  {
        $result = mysqli_query($conn, $sql);

        if (!$result)
        {
            throw new Exception(mysqli_error($conn).'  -  '.$sql);
        }

        return $result;
  }

  function getSqlOrdenesAsociadas($orden, $self = null)
  {
        $sql = "SELECT id_orden_asociada as id_ord
                from ordenes_abiertas_sur
                where id_orden in(
                                    SELECT id_orden
                                    FROM ordenes_abiertas_sur o
                                    where id_orden_asociada = $orden
                                 )
                union
                select id_orden_asociada
                from ordenes_abiertas_sur
                where id_orden = $orden
                union
                select id_orden
                from ordenes_abiertas_sur
                where id_orden_asociada = $orden";
        if ($self)
        {
            $sql.= ' SELECT $orden';
        }

        return $sql;
  }

  function nextId($conn, $tableName, $schemaName)
  {
        $sqlNexIdOrden = "SELECT auto_increment from information_schema.TABLES where TABLE_NAME = '$tableName' and TABLE_SCHEMA = '$schemaName'";
        $result = mysqli_query($conn, $sqlNexIdOrden);

        if ($row = mysqli_fetch_array($result))
        {
            return $row['auto_increment'];

        }
        else
        {
            throw new Exception('No se pudo recuperar el proximo ID de la tabla '.$tableName);
        }
  }

  function updateNombresGrupoOrdenes($orden, $conn)
  {
        $sql = "SELECT id_ord, nombre, citacion
                from(
                SELECT id_orden_asociada as id_ord
                                from ordenes_abiertas_sur
                                where id_orden in(
                                                    SELECT id_orden
                                                    FROM ordenes_abiertas_sur o
                                                    where id_orden_asociada = $orden
                                                 )
                                union
                                select id_orden_asociada
                                from ordenes_abiertas_sur
                                where id_orden = $orden
                                union
                                select id_orden
                                from ordenes_abiertas_sur
                                where id_orden_asociada = $orden
                union
                select $orden) ord
                join horarios_ordenes_sur hhs ON hhs.id_orden = ord.id_ord
                join ordenes o ON o.id = ord.id_ord
                order by citacion, o.id";
        //mysqli_begin_transaction($conn);
                mysqli_autocommit($conn, false);
        try
        {
            $result = executeSql($conn, $sql);
            $first = $exist = false;

            $count = mysqli_num_rows($result);
            $i = 1;

            $parts = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X'];

            $last = $actualizar = null;

            foreach ($result as $ord)
            {
                $str = $ord['nombre'];
                $pos = strrpos($str, "-");
                $sub = substr($str, 0, $pos-1);

                //Se utiliza para corregir el destino de la siguiente orden, como asi tambien los horarios
                if (($last) && (!$exist))
                {
                    if ($last['id_ord'] == $orden)
                    {
                        $exist = true;
                        $actualizar = $ord;
                    }
                }

                if (!$exist)
                {
                    $last = $ord;
                }

                //Fin de correccion

                if (!$first)
                {
                    $name = $sub.' - INICIO';
                    $first = true;
                }
                else
                {
                    if ($count == $i)
                    {
                        $name = $sub.' - FIN';
                    }
                    else
                    {
                        $name = $sub.' - PARTE '.$parts[$i];
                    }
                }

                $update = "UPDATE ordenes SET nombre = '$name' WHERE id = $ord[id_ord]";

                executeSql($conn, $update);
                $i++;
            }

            mysqli_commit($conn);

            if (($exist) && ($last) && ($actualizar))
            {
                $sqlDestinoUltimo = "SELECT id_ciudad_destino, id_estructura_ciudad_destino FROM ordenes WHERE id = $last[id_ord]";
                $resultDestino = executeSql($conn, $sqlDestinoUltimo);
                if ($row = mysqli_fetch_array($resultDestino))
                {
                    $updateOrigen = "UPDATE ordenes SET id_ciudad_origen = $row[id_ciudad_destino], id_estructura_ciudad_origen = $row[id_estructura_ciudad_destino] WHERE id = $actualizar[id_ord]";

                    executeSql($conn, $updateOrigen);
                }

                ///falta actualizar la ultima orden con los datos de la anterior
            }
        }
        catch (Exception $e){
                                throw $e;
        }
  }

  function asignarRecursos($orden, $lastid, $conn, $asoc = null)
  {
    try
    {
        ///debe asignar los horarios auxiliares 
        $sqlInsertHorarios = "INSERT INTO horarios_ordenes_sur (".getCamposHorariosOrdenesSur().") SELECT $lastid, finalizacion, finalizacion, finalizacion, finalizacion, id_estructura_orden, finalizacion_real, finalizacion_real, finalizacion_real, finalizacion_real, cod_servicio FROM horarios_ordenes_sur WHERE id_orden = $orden";
        executeSql($conn, $sqlInsertHorarios);

        //debe asignar la tripulacion
        $sqlTripulacion = "SELECT * FROM tripulacionXOrdenes WHERE id_orden = $orden";
        $resultTripulacion = executeSql($conn, $sqlTripulacion);
        foreach ($resultTripulacion as $trip)
        {
            $sqlAddTrip = "INSERT INTO tripulacionXOrdenes (id_orden, id_estructura_orden, id_empleado) VALUES ($lastid, $trip[id_estructura_orden], $trip[id_empleado])";
            executeSql($conn, $sqlAddTrip);
        }

        if ($asoc)
        {
            $orden = $asoc;
        }

        //debe asignar la nueva orden a la orden original
        $sqlAsoc = "INSERT INTO ordenes_abiertas_sur (id_orden, id_estructura_orden, id_orden_asociada, id_esructura_orden_asociada) VALUES ($orden, $_SESSION[structure], $lastid, $_SESSION[structure])";
        executeSql($conn, $sqlAsoc);
    }
    catch (Exception $e) {
                            throw $e;
    }
  }

  if (isset($_POST['accion']))
  {

      if ($_POST['accion'] == 'open')
      {
            $conn = conexcion(true);

            //Primero debe buscar si la orden ya ha sido abierta
            $sqlOpened = getSqlOrdenesAsociadas($_POST['orden']);
            $resultOpened = executeSql($conn, $sqlOpened);
            if (mysqli_num_rows($resultOpened)) //la orden ya ha sido abierta
            {
                $idOriginal = $_POST['orden'];

                //debe buscar primero si es la original o una dependiente
                $sqlDependence = "SELECT id_orden
                                 FROM ordenes_abiertas_sur o
                                 where id_orden_asociada = $_POST[orden]";
                $resultDependence = executeSql($conn, $sqlDependence);  
                if ($row = mysqli_fetch_array($resultDependence))
                {
                    $idOriginal = $row['id_orden'];
                }

                //mysqli_begin_transaction($conn);
                mysqli_autocommit($conn, false);
                try
                {
                    $lastid = nextId($conn, 'ordenes', getBD());
                    $insert = "INSERT INTO ordenes (id, ".getCamposOren(false).") SELECT $lastid, ".getCamposOren(false, true)." FROM ordenes WHERE id = $_POST[orden]";  

                    executeSql($conn, $insert);   
                    asignarRecursos($_POST['orden'], $lastid, $conn, $idOriginal);
                    mysqli_commit($conn);

                    updateNombresGrupoOrdenes($_POST['orden'], $conn);

                    mysqli_close($conn);
                    return print json_encode(['ok' => true, 'message' => 'ejecutada ok']);
                }
                catch (Exception $e) {
                                        mysqli_rollback($conn);
                                        mysqli_close($conn);
                                        return print json_encode(['ok' => false, 'message' => $e->getMessage()]);
                }


            }
            else   //La orden no ha sido abierta aun
            {
                $sqlEsclava = "SELECT * 
                               FROM ordenes_abiertas_sur oas
                               JOIN ordenes ord ON ord.id = oas.id_orden_asociada
                               WHERE id_orden_asociada = $_POST[orden] AND not ord.borrada";
                $resultEsclava = executeSql($conn, $sqlEsclava);



                if (!mysqli_num_rows($resultEsclava)) //La orden aun no ha sido abierta
                {
                    mysqli_autocommit($conn, false);
                    //return print json_encode(['ok' => false, 'message' => 'Error desconocido - PRE BEGIN']);
                    //mysqli_begin_transaction($conn);


                    try
                    {
                        $lastid = nextId($conn, 'ordenes', getBD());
                        $insert = "INSERT INTO ordenes (id, ".getCamposOren(false).") SELECT $lastid, ".getCamposOren(' - FIN', true)." FROM ordenes WHERE id = $_POST[orden]";                        
                        executeSql($conn, $insert);                        
                        $update = "UPDATE ordenes SET nombre = concat(nombre,' - INICIO') WHERE id = $_POST[orden]";
                        executeSql($conn, $update);
                        asignarRecursos($_POST['orden'], $lastid, $conn);
                        mysqli_commit($conn);
                        mysqli_close($conn);
                        return print json_encode(['ok' => true, 'message' => 'ejecutada ok']);
                    }
                    catch (Exception $e) {
                                            mysqli_rollback($conn);
                                            mysqli_close($conn);
                                            return print json_encode(['ok' => false, 'message' => $e->getMessage()]);
                    }
                }
            }

      }
      elseif ($_POST['accion'] == 'city')
      {

            $conn = conexcion(true);
            $campo = $_POST['campo'];
            $campoStr = $_POST['campoStr'];
            $value = $_POST['value'];
            $orden = $_POST['orden'];

            $sql = "UPDATE ordenes SET $campo = $value, $campoStr = $_SESSION[structure] WHERE id = $orden";
            //return print $sql;
            $result = mysqli_query($conn, $sql);
      }
  }
  else
  {
          $id = $_POST['nroorden'];

          $hcitacion = DateTime::createFromFormat('d/m/Y H:i', $_POST['hcitacion']);
          $fecha = $hcitacion->format('Y-m-d');
          $hcitacion = $hcitacion->format('Y-m-d H:i');

          

          $conn = conexcion();
          if (isset($_POST['borrada']))
          {
              $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1";
              $result = mysql_query($sql, $conn);
              if ($data = mysql_fetch_array($result)){
                 $_SESSION['senmail'] = 1;
              }
              else{
                  $_SESSION['senmail'] = 0;
              }
          }
          
             $res_cc = ejecutarSQL("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]", $conn);
             if ($data_cc = mysql_fetch_array($res_cc)){
                 $cantTripulacion = $data_cc[0];
             }

          $nombre = $_POST['nombre'];

          $hcitacion = DateTime::createFromFormat('d/m/Y H:i', $_POST['hcitacion']);
          $hcitacion = $hcitacion->format('Y-m-d H:i');

          $hsalida = DateTime::createFromFormat('d/m/Y H:i', $_POST['hsalida']);
          $hsalida = $hsalida->format('Y-m-d H:i');

          $hllegada = DateTime::createFromFormat('d/m/Y H:i', $_POST['hllegada']);
          $hllegada = $hllegada->format('Y-m-d H:i');

          $hfinserv = DateTime::createFromFormat('d/m/Y H:i', $_POST['hfinserv']);
          $hfinserv = $hfinserv->format('Y-m-d H:i');  

          $km = $_POST['km'];
          $chofer1 = ($_POST['chofer1']) ? $_POST['chofer1'] : 'NULL';
          $chofer2 = ($_POST['chofer2']) ? $_POST['chofer2'] : 'NULL';
          $interno = ($_POST['interno']) ? $_POST['interno'] : 'NULL';
          $clivacio = isset($_POST['clivac']) ? $_POST['clivac'] : 'NULL';

          if (isset($_POST['clivac']))
          {
              if ($_POST['clivac'])
              {
                 $sql_cli = "SELECT razon_social FROM clientes c where id = $_POST[clivac] and id_estructura = $_SESSION[structure]";
                // $conn = conexcion();
                 $result_cli = mysql_query($sql_cli, $conn);
                // mysql_close($conn);
                 $data_cli = mysql_fetch_array($result_cli);
                 $nom_cliente = $data_cli['razon_social'];
                 $pos = strpos($nombre, '~');
                 if ($pos){
                    $nombre=substr_replace($nombre, "($nom_cliente)", ($pos+1), strlen($nombre));
                 }
                 else{
                      $nombre.= "~($nom_cliente)";
                 }
              }
              else{
                   $pos = strpos($nombre,'~');
                   if ($pos){
                      $nombre = substr($nombre, 0, ($pos));
                   }
              }
          }

          //$nombre = htmlentities($nombre);
          $final = isset($_POST['finalizada']) ? 1 : 0;
          $borra = isset($_POST['borrada']) ? 1 : 0;

          $campos = "id_user, fecha_accion, fservicio, nombre, km, id_chofer_1, id_chofer_2, id_micro, id_cliente_vacio, finalizada, borrada, comentario";
          $values = "$_SESSION[userid], now(), '$fecha', '".$nombre."', $km, $chofer1, $chofer2, $interno, $clivacio, $final, $borra, '$_POST[obs]'";

          backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])", $conn);
          $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])", $conn);
        //die('oerwdws    rk');
          try{
            $delete = "DELETE FROM tripulacionXOrdenes WHERE id_orden = $id AND id_estructura_orden = $_SESSION[structure]";
            ejecutarSQL($delete, $conn);
            }catch (Exception $e){die($e->getMessage());}
          

          for ($i = 3; $i <= $cantTripulacion; $i++){
              $conductor = $_POST["chofer$i"];
              if ($conductor){
                $insert = "INSERT INTO tripulacionXOrdenes (id_orden, id_estructura_orden, id_empleado) VALUES ($id, $_SESSION[structure], $conductor)";
                ejecutarSQL($insert, $conn);
              }
          }
          
          ////////////////////
         $sqlEstado = "SELECT id, fecha
                       FROM estadoDiagramasDiarios
                       WHERE (fecha = (SELECT date(citacion) FROM horarios_ordenes_sur where id_orden = $id)) and (finalizado = 1) and (id_estructura = $_SESSION[structure])";

         $resultEstado = mysql_query($sqlEstado, $conn);

         $cerrado = mysql_num_rows($resultEstado);

        $cita = 'citacion';
        $sale = 'salida';
        $llega = 'llegada';
        $fina = 'finalizacion';
        if ($cerrado)
        {
            $cita = 'citacion_real';
            $sale = 'salida_real';
            $llega = 'llegada_real';
            $fina = 'finalizacion_real'; 
        }
        //////////////

          $upd = "UPDATE horarios_ordenes_sur SET $cita = '$hcitacion', $sale = '$hsalida', $llega = '$hllegada', $fina = '$hfinserv', cod_servicio = '$_POST[cod_servicio]' WHERE id_orden = $id AND id_estructura_orden = $_SESSION[structure]";
          try{
                ejecutarSQL($upd, $conn);
          }catch (Exception $e){die($e->getMessage());}



          $sql = "SELECT id_orden_vacio FROM ordenesAsocVacios where id_orden = $id and id_estructura_orden = $_SESSION[structure]";  ///recupera todas las ordenes de vacios asociadas
          $result = ejecutarSQL($sql, $conn);
          $ordenes_vacios = "";
          while ($row = mysql_fetch_array($result)){
                if ($ordenes_vacios){
                   $ordenes_vacios.= ",$row[0]";
                }
                else{
                     $ordenes_vacios = "$row[0]";
                }
          }
          
          if ($ordenes_vacios){  ///significa que al menos tiene una orden asociada
             $campos = "id_user, fecha_accion, id_chofer_1, id_chofer_2, id_micro, borrada";
             $values = "$_SESSION[userid], now(), $chofer1, $chofer2, $interno, $borra";
             backup('ordenes', 'ordenes_modificadas', "(id in ($ordenes_vacios)) and (id_estructura = $_SESSION[structure])", $conn);
             $res = update("ordenes", $campos, $values, "(id in ($ordenes_vacios)) and (id_estructura = $_SESSION[structure])", $conn);
          }


          if ($_POST['asocia']){
             //$conn = conexcion();
             $sql = "SELECT o.id, o.id_estructura
                     FROM ordenes_asocioadas oa
                     INNER JOIN ordenes o ON o.id = oa.id_orden_asociada and o.id_estructura = oa.id_esructura_orden_asociada
                     WHERE id_orden = $id and id_estructura_orden = $_SESSION[structure]";
             try{
                // begin($conn);
                 $campos = "id_chofer_1, id_chofer_2, id_micro";
                 $values = "$chofer1, $chofer2, $interno";
                 $result = ejecutarSQL($sql, $conn);
                 while ($row = mysql_fetch_array($result)){
                       backup('ordenes', 'ordenes_modificadas', "(id = $row[id]) and (id_estructura = $row[id_estructura])", $conn);
                       update("ordenes", $campos, $values, "(id = $row[id]) and (id_estructura = $row[id_estructura])", $conn);
                 }
               //  commit($conn);
             }catch (Exception $e) {
                                  //  roolback($conn);
                                   }

          }

               cerrarconexcion($conn);
          if ($borra && $res){
             sentMail($id);
          }
          
          print json_encode(['ok' => $res]);
      }
?>

