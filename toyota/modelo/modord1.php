<?
  session_start();
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');

  $id = $_POST['orden'];
  $horario = $_POST['horarios'];
  $pax = $_POST['pax'];
  $i_v = $_POST['iv'];
  $campos = "finalizada, cantpax";
  if ($i_v == 'i'){
     $campos.=", hllegada";
  }
  else{
       $campos.=", hsalida";
  }

  $values = "1, $pax, '$horario'";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  echo update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
?>

