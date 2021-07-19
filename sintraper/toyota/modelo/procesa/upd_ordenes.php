<?php
  session_start();
  include ('../../../controlador/bdadmin.php');
  include_once ('../../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);

  $data  = explode("-",$_POST['id']);

  $campo = $data[0]; // nombre del campo
  $id    = $data[1]; // id del registro
  $value = $_POST['value']; // valor por el cual reemplazar


 // $query = mysql_query("UPDATE ordenes SET $campo = '$value'  WHERE (id = $id) and (id_estructura = $_SESSION[structure])") or die(mysql_error($conn));
  backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
  update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
  $conn = conexcion();
  if (($campo == 'id_chofer_1') || ($campo == 'id_chofer_2')){
     $result = mysql_query("SELECT upper(concat(apellido,', ', nombre)) as apenom FROM empleados WHERE (id_empleado = $value) and (id_estructura = ".STRUCTURED.")");
     if ($data = mysql_fetch_array($result)){
         $res = $data['apenom'];
         mysql_close($conn);
         echo $res;
     }
  }
  else{
         if ($campo == 'id_micro'){
     $result = mysql_query("SELECT interno FROM unidades where id = $value");
     if ($data = mysql_fetch_array($result)){
         $res = $data[0];
         mysql_close($conn);
         echo $res;
     }
  }
  else{
       mysql_close($conn);
       echo $value;
       }
       }
?>
