<?php
  session_start();
  include ('../../controlador/bdadmin.php');
  define(STRUCTURED, $_SESSION['structure']);
  $conn = conexcion();
  $data  = explode("-",$_POST['id']);

  $campo = $data[0]; // nombre del campo
  $id    = $data[1]; // id del registro
  $value = $_POST['value']; // valor por el cual reemplazar

  $query = mysql_query("UPDATE clientes SET ".$campo." = '".$value."'  WHERE (id = '".$id."') and (id_estructura = ".STRUCTURED.")") or die(mysql_error($conn));

  if ($campo == 'id_responsabilidadIva'){
     $result = mysql_query("SELECT upper(responsabilidad) as resp FROM responsabilidadiva where id = $value");
     if ($data = mysql_fetch_array($result)){
         $res = $data['resp'];
         mysql_close($conn);
         echo $res;
     }
  }
  else{
       mysql_close($conn);
       echo $value;
       }
?>
