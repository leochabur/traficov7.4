<?php
  session_start();
  //include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

  $fecha = $_POST['fecha'];

  $del = "UPDATE ordenes SET borrada = 1, fecha_accion = now(), id_user = $_SESSION[userid] where fservicio = '$fecha' and id_estructura = $_SESSION[structure]";

  ejecutarSQL($del);
?>

