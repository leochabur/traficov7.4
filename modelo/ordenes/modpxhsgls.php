<?php
  session_start();
  //include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

  $id = $_POST['orden'];
  
  $pax = $_POST['cantpax'];
  $reca = $_POST['reca'];
  
  $campos = "id_user, fecha_accion, cantpax";
  $values = "$_SESSION[userid], now(), $pax";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");

  $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
  
  $sql="INSERT INTO recaudacionxorden (id_orden, monto, id_estructura_orden) VALUES ($id, $reca, $_SESSION[structure]) ON DUPLICATE KEY UPDATE monto=$reca";
  
  ejecutarSQL($sql);
  print $res;
?>

