<?php
  session_start();
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

  $id = $_POST['orden'];
  

  $hsalida = $_POST['hsalida'];
  $hllegada = $_POST['hllegada'];
  $hfinserv = $_POST['hfinserv'];
  $pax = $_POST['cantpax'];
  $obs = $_POST['com'];

  $addobs = "INSERT INTO obsSupervisores (id_orden, id_usuario, fecha_accion, comentario) VALUES ($id, $_SESSION[userid], now(), '$obs')
             ON DUPLICATE KEY UPDATE id_usuario = $_SESSION[userid], fecha_accion = now(), comentario = '$obs'";
  $conn = conexcion();
  try
  {
      if ($obs)
         ejecutarSQL($addobs, $conn) or die($addobs);
      else
          ejecutarSQL("DELETE FROM obsSupervisores WHERE id_orden = $id", $conn) or die("error");
      
      $campoHora = "hsalidaplantareal";
      $valorHora = "$hsalida";
      if ($_POST['iv'] == 'i')
      {
         $campoHora = "hllegadaplantareal";
         $valorHora = "$hllegada";
      }
      $campos = "id_user, fecha_accion, $campoHora, cantpax";
      $values = "$_SESSION[userid], now(), '$valorHora', $pax";
      $res = backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])", $conn);
      $res = update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])", $conn);

      if ($_POST['iv'] != 'i') //esta saliendo de planta, si la hora de salida es posterior a la hora actual, debe informar el cambio a pax tracker ya que se asume que se esta modificando la hora de salida
      { 
          comunicateInsertsWhitIdVerifica($id, $conn, 'SE ACTUALIZO HORAD DE SALIDA');
      }
      mysql_close($conn);
  }
  catch (Exception $e){
                        die($e->getMessage());
                      }
  
  print $res;
?>

