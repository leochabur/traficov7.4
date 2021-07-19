<?
  session_start();
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');

  $id = $_POST['nroorden'];
  $fecha = dateToMysql($_POST['fservicio'], '/');
  $nombre = $_POST['nombre'];
  $hcitacion = $_POST['hcitacion'];
  $hsalida = $_POST['hsalida'];
  $hllegada = $_POST['hllegada'];
  $hfinserv = $_POST['hfinserv'];
  $km = $_POST['km'];
  $chofer1 = ($_POST['chofer1']) ? $_POST['chofer1'] : 'NULL';
  $chofer2 = ($_POST['chofer2']) ? $_POST['chofer2'] : 'NULL';
  $interno = ($_POST['interno']) ? $_POST['interno'] : 'NULL';

  $campos = "fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_chofer_1, id_chofer_2, id_micro";
  $values = "'$fecha', '$nombre', '$hcitacion', '$hsalida', '$hllegada', '$hfinserv', $km, $chofer1, $chofer2, $interno";

  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = $_SESSION[structure])");
  echo update("ordenes", $campos, $values, "(id = $id) and (id_estructura = $_SESSION[structure])");
?>

