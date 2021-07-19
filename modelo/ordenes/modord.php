<?php
  session_start();
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/enviomail/sendordbjafn.php');
  //include ('../../modelsORM/call.php');  
  //include_once ('../../modelsORM/controller.php');    


  $id = $_POST['nroorden'];
  $conn = conexcion();
  if (isset($_POST['borrada']))
  {
      $fechaInicio = DateTime::createFromFormat('Y-m-d', '2020-11-01');
      $sql = "SELECT tipoServicio, o.fservicio
              from ordenes o
              inner join servicios s ON o.id_servicio = s.id AND o.id_estructura_servicio = s.id_estructura
              inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
              where o.id = $id and tipoServicio = 'charter'";
      $result = ejecutarSQL($sql, $conn);
      if ($row = mysql_fetch_array($result))
      {
        $fechaOrden = DateTime::createFromFormat('Y-m-d', $row['fservicio']);
        if ($fechaOrden > $fechaInicio)
        {
          mysql_close($conn);
          print json_encode(array('ok' => false, 'message' => 'El servicio se encuentra ya a la venta, no se puede eliminar'));
          return;
        }
      }
  }

  $fecha = dateToMysql($_POST['fservicio'], '/');
  
  $diagramaFinalizado = 0;

  $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1 and id_estructura = $_SESSION[structure]";
  $result = mysql_query($sql, $conn);  

  if ($data = mysql_fetch_array($result))
  {
    $diagramaFinalizado = 1;
  }

  if (isset($_POST['borrada'])){
      if ($diagramaFinalizado){
         $_SESSION['senmail'] = 1;         
      }
      else{
          $_SESSION['senmail'] = 0;
      }
  }
  
  
  $nombre = $_POST['nombre'];



  $hcitacion = $_POST['hcitacion'];
  $hsalida = $_POST['hsalida'];
  $hllegada = $_POST['hllegada'];
  $hfinserv = $_POST['hfinserv'];

  $hcitacionDiag = $_POST['hcitacionDiagrama'];
  $hsalidaDiag = $_POST['hsalidaDiagrama'];
  $hllegadaDiag = $_POST['hllegadaDiagrama'];
  $hfinservDiag = $_POST['hfinservDiagrama'];  
  
  $km = $_POST['km'];
  $chofer1 = ($_POST['chofer1']) ? $_POST['chofer1'] : 'NULL';
  $chofer2 = ($_POST['chofer2']) ? $_POST['chofer2'] : 'NULL';
  $interno = ($_POST['interno']) ? $_POST['interno'] : 'NULL';
  $clivacio = ($_POST['clivac']) ? $_POST['clivac'] : 'NULL';

  if (isset($_POST['clivac']))
  {
      if ($_POST['clivac']){
         $sql_cli = "SELECT razon_social FROM clientes c where id = $_POST[clivac] and id_estructura = $_SESSION[structure]";
         $result_cli = mysql_query($sql_cli, $conn);
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

  ////////////////si eldiaramaesta finalizado actualiza losorarios reales
  if ($diagramaFinalizado){
    $camposHorario = "hcitacionreal, hsalidaplantareal, hllegadaplantareal,hfinservicioreal ";
    $valuesHorario = "'$hcitacion', '$hsalida', '$hllegada', '$hfinserv'";
  }
  else{
    $camposHorario = "hcitacion, hsalida, hllegada, hfinservicio";
    $valuesHorario = "'$hcitacion', '$hsalida', '$hllegada', '$hfinserv'";
  }

  $campos = "id_user, fecha_accion, fservicio, nombre, $camposHorario, km, id_chofer_1, id_chofer_2, id_micro, id_cliente_vacio, finalizada, borrada";
  $values = "$_SESSION[userid], now(), '$fecha', '$nombre', $valuesHorario, $km, $chofer1, $chofer2, $interno, $clivacio, $final, $borra";

  try{
  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])", $conn);
  $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])", $conn);

     }catch (Exception $e) {
                          //  roolback($conn);
                          die($e->getMessage());
                           }

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
     $campos = "id_user, fecha_accion, id_chofer_1, id_chofer_2, id_micro";
     $values = "$_SESSION[userid], now(), $chofer1, $chofer2, $interno";
     if ($borra)
     {
        $campos.=", borrada";
        $values.=", $borra";
     }
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
                          die($row->getMessage());
                           }

  }

       
  if ($borra && $res){
    // sentMail($id);
  }

  if ($diagramaFinalizado)
  { 
    if (isset($_POST['borrada']))
    { 
        comunicateDelete($id, $conn, 'modord.php');
    }
    else
    {
        comunicateInsertsWhitId($id, $conn, 'UPDATE FROM: modord.php');
    }  
  }

  cerrarconexcion($conn);
  print json_encode(array('ok' => true));
  return;
?>

