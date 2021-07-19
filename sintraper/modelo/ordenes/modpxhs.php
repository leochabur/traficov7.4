<?php
  session_start();
  //include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

  $id = $_POST['orden'];
  

  $hsalida = $_POST['hsalida'];
  $hllegada = $_POST['hllegada'];
  $hfinserv = $_POST['hfinserv'];
  $pax = $_POST['cantpax'];
  $obs = $_POST['com'];

  $addobs = "INSERT INTO obsSupervisores (id, id_orden, id_usuario, fecha_accion, comentario) VALUES ($id, $id, $_SESSION[userid], now(), '$obs')
             ON DUPLICATE KEY UPDATE id_usuario = $_SESSION[userid], fecha_accion = now(), comentario = '$obs'";

  if ($obs != '')
     ejecutarSQL($addobs);
  else
      ejecutarSQL("DELETE FROM obsSupervisores WHERE id = $id");
  
  $campos = "id_user, fecha_accion, hsalidaplantareal, hllegadaplantareal, cantpax";
  $values = "$_SESSION[userid], now(), '$hsalida', '$hllegada', $pax";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
  
  print $res;
?>

