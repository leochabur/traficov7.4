<?php
  session_start();
  //include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

  $fecha = $_POST['fecha'];

  $del = "delete FROM ordenes where fservicio = '$fecha' and id_estructura = $_SESSION[structure]";

  ejecutarSQL($del);
?>

