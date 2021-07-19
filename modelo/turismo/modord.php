<?
  session_start();
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');



  $id = $_POST['nroorden'];
  
  $nro = $_POST['oc'];

 //die("INSERT INTO numeroOC (id_orden, id_estructura_orden, numero) VALUES ($id, $_SESSION[structure], '$nro') ON DUPLICATE KEY UPDATE numero = '$nro'");
  ejecutarSQL("INSERT INTO numeroOC (id_orden, id_estructura_orden, numero) VALUES ($id, $_SESSION[structure], '$nro') ON DUPLICATE KEY UPDATE numero = '$nro'");
  
?>

