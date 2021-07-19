<?
  session_start();
  include ('../../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  $data  = explode("-",$_POST['id']);

  $campo = $data[0]; // nombre del campo
  $id    = $data[1]; // id del registro
  $value = $_POST['value']; // valor por el cual reemplazar
  
  $campo = "$campo, id_user, fecha_accion";
  $value = "$value, $_SESSION[userid], now()";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  
  update("ordenes", "$campo", "'$value'", "(id = '$id') and (id_estructura = $_SESSION[structure])");

  //echo $value;
?>

