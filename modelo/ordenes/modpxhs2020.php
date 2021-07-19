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

  $addobs = "INSERT INTO obsSupervisores (id_orden, id_usuario, fecha_accion, comentario) VALUES ($id, $_SESSION[userid], now(), '$obs')
             ON DUPLICATE KEY UPDATE id_usuario = $_SESSION[userid], fecha_accion = now(), comentario = '$obs'";
  try{
  if ($obs)
     ejecutarSQL($addobs) or die($addobs);
  else
      ejecutarSQL("DELETE FROM obsSupervisores WHERE id_orden = $id") or die("error");
  
  $campoHora = "hsalidaplantareal";
  $valorHora = "$hsalida";
  if ($_POST['iv'] == 'i'){
     $campoHora = "hllegadaplantareal";
     $valorHora = "$hllegada";
  }
  $campos = "id_user, fecha_accion, $campoHora, cantpax";
  $values = "$_SESSION[userid], now(), '$valorHora', $pax";
 // die($campos." ".$values);

  $res = backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
  }catch (Exception $e){
                        die($e->getMessage());
         }
  
  print $res;
?>

