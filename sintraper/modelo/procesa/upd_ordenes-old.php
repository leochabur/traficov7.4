<?php
  session_start();
  include ('../../controlador/bdadmin.php');
  define(STRUCTURED, $_SESSION['structure']);
  $conn = conexcion();
  $data  = explode("-",$_POST['id']);

  $campo = $data[0]; // nombre del campo
  $id    = $data[1]; // id del registro
  $value = $_POST['value']; // valor por el cual reemplazar
  $conductor = '';
  if (($campo == 'id_chofer_1') || ($campo == 'id_chofer_2'))
     $conductor = ', id_estructura_chofer1 = '.STRUCTURED;
  $query = mysql_query("UPDATE ordenes SET ".$campo." = '".$value."'$conductor  WHERE (id = '".$id."') and (id_estructura = ".STRUCTURED.")");

  if (($campo == 'id_chofer_1') || ($campo == 'id_chofer_2')){
     $result = mysql_query("SELECT upper(concat(apellido,', ', nombre)) as apenom FROM empleados WHERE (id_empleado = $value) and (id_estructura = ".STRUCTURED.")");
     if ($data = mysql_fetch_array($result)){
         $res = $data['apenom'];
         mysql_close($conn);
         echo $res;
     }
  }
  else{
       mysql_close($conn);
       echo $value;
       }
?>
