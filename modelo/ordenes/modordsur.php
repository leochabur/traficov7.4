<?php
  session_start();
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

  $id = $_POST['nroorden'];
  $fecha = dateToMysql($_POST['fservicio'], '/');
  $conn = conexcion();
  if (isset($_POST['borrada'])){
      $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1";
     // die($sql);
      $result = mysql_query($sql, $conn);
    //  mysql_close($conn);
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
  $clivacio = ($_POST['clivac']) ? $_POST['clivac'] : 'NULL';
  if (isset($_POST['clivac'])){
  if ($_POST['clivac']){
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
  

  $upd = "UPDATE horarios_ordenes_sur SET citacion = '$hcitacion', salida = '$hsalida', llegada = '$hllegada', finalizacion = '$hfinserv' WHERE id_orden = $id AND id_estructura_orden = $_SESSION[structure]";
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
  
  print $res;
?>

