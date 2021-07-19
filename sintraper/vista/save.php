<?php
include("conexion.php");

$data  = explode("-",$_POST['id']);

$campo = $data[0]; // nombre del campo
$id    = $data[1]; // id del registro
$value = $_POST['value']; // valor por el cual reemplazar

$query = mysql_query("UPDATE certMedicos SET ".$campo." = '".$value."'
							WHERE id = '".$id."'");

if ($campo == 'id_medico'){
   $result = mysql_query("SELECT upper(concat(apellido,', ',nombre)) as medico FROM medicos WHERE id = $value");
   if ($data = mysql_fetch_array($result)){
      echo $data['medico'];
   }
}
elseif ($campo == 'id_ctroAsis'){
   $result = mysql_query("SELECT upper(ctrosAsistenciales) as centro FROM medicos WHERE id = $value");
   if ($data = mysql_fetch_array($result)){
      echo $data['centro'];
   }
}
elseif ($campo == 'id_diagnostico'){
   $result = mysql_query("SELECT UPPER(diagnostico) as diag FROM diagnosticos WHERE id = $value");
   if ($data = mysql_fetch_array($result)){
      echo $data['diag'];
   }
}
else
echo $value;
?>
