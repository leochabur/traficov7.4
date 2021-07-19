<?php
  session_start();
  //include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

  $id = $_POST['orden'];
  

  $hsalida = $_POST['hsalida'];
  $hllegada = $_POST['hllegada'];
  $hfinserv = $_POST['hfinserv'];
  $pax = $_POST['cantpax'];


  $campos = "id_user, fecha_accion, hsalida, hllegada, cantpax";
  $values = "$_SESSION[userid], now(), '$hsalida', '$hllegada', $pax";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
  
  print $res;
?>

