<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/enviomail/sendordbjafn.php');



  $id = $_POST['nroorden'];
  $fecha = dateToMysql($_POST['fservicio'], '/');
  
  if (isset($_POST['borrada'])){
     $conn = conexcion();
      $sql = "SELECT * FROM estadoDiagramasDiarios e where fecha = '$fecha' and id_estado = 1";
      $result = mysql_query($sql, $conn);
      mysql_close($conn);
      if ($data = mysql_fetch_array($result)){
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
  $km = $_POST['km'];
  $chofer1 = ($_POST['chofer1']) ? $_POST['chofer1'] : 'NULL';
  $chofer2 = ($_POST['chofer2']) ? $_POST['chofer2'] : 'NULL';
  $interno = ($_POST['interno']) ? $_POST['interno'] : 'NULL';
  $clivacio = ($_POST['clivac']) ? $_POST['clivac'] : 'NULL';
  if ($_POST['clivac']){
     $sql_cli = "SELECT razon_social FROM clientes c where id = $_POST[clivac] and id_estructura = $_SESSION[structure]";
     $conn = conexcion();
     $result_cli = mysql_query($sql_cli, $conn);
     mysql_close($conn);
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
  $nombre = utf8_encode($nombre);
  $final = isset($_POST['finalizada']) ? 1 : 0;
  $borra = isset($_POST['borrada']) ? 1 : 0;

  $campos = "id_user, fecha_accion, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_chofer_1, id_chofer_2, id_micro, id_cliente_vacio, finalizada, borrada";
  $values = "$_SESSION[userid], now(), '$fecha', '".$nombre."', '$hcitacion', '$hsalida', '$hllegada', '$hfinserv', $km, $chofer1, $chofer2, $interno, $clivacio, $final, $borra";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
  if ($borra && $res){
     sentMail($id);
  }
  
  print $res;
?>

