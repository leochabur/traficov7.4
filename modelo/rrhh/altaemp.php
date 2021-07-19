<?
  session_start();

  include ('../../controlador/ejecutar_sql.php');
  include_once('../utils/dateutils.php');

  define(STRUCTURED, $_SESSION['structure']);//reemplazar por $_SESSION['structure']

  $video = $_POST['video'] ? 1 : 0;
  $banio = $_POST['banio'] ? 1 : 0;
  
  $city = isset($_POST['ciudad']) ? $_POST['ciudad'] : 'null';
  $naci = isset($_POST['nacion']) ? $_POST['nacion'] : 'null';

  $fnac = isset($_POST['fnac']) ? dateToMysql($_POST['fnac'], '/') : "null";
  $fini = isset($_POST['fini']) ? dateToMysql($_POST['fini'], '/') : "null";
  
  $estructura = STRUCTURED;
  $procesado = 0;
  $usr = "usuario_alta_provisoria";

  if ($_SESSION['modaf'] == 1){
     $usr = "usuario_alta_definitiva ";
     $procesado = 1;
  }
  
  $afectara = $estructura;
  if (isset($_POST['struct'])){
     $afectara = $_POST['struct'];
  }

  $campos= "id_empleado, $usr, legajo, domicilio, id_ciudad, telefono, id_nacionalidad, sexo, fechanac, tipodoc, nrodoc, cuil, activo, id_sector, id_cargo, id_empleador, inicio_relacion_laboral, apellido, nombre, fecha_alta, id_estructura, id_estructura_empleador, procesado, id_estructura_cargo, id_estructura_ciudad, afectado_a_estructura, borrado, email";
  $values= "$_SESSION[userid],$_POST[legajo], '$_POST[dire]', $city, '$_POST[tele]', '$naci', '$_POST[sexo]', '$fnac', '$_POST[tipodoc]', '$_POST[nrodoc]', '$_POST[cuit]', 1, null, $_POST[puesto], $_POST[empleador], '$fini', '$_POST[ape]', '$_POST[nom]', now(), $afectara, $estructura, $procesado, $estructura, $estructura, $afectara, 0, '$_POST[mail]'";


  $cliente =  insert("empleados", $campos, $values);
  
  print json_encode($cliente);

?>

