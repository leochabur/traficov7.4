<?php
  session_start();
  include ('../../controlador/ejecutar_sql.php');
  define(STRUCTURED, $_SESSION['structure']);
  //$conn = conexcion();
 // die($_POST['id']);
  $data  = explode("-",$_POST['id']);

  $campo = $data[0]; // nombre del campo
  $id    = $data[1]; // id del registro
  $value = $_POST['value']; // valor por el cual reemplazar

  if ($campo == 'id_micro'){
     $struct = STRUCTURED;
     if ($value == 0){
        $value = 'NULL';
        $struct = 'NULL';
     }
     $campo.=', id_user, fecha_accion';
     $value.=", $_SESSION[userid], now()";
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print json_encode(update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")"));
  }
  elseif($campo == 'id_chofer_1'){
     $struct = STRUCTURED;
     if ($value == 0){
        $value = 'NULL';
        $struct = 'NULL';
     }
     $campo.=', id_estructura_chofer1, id_user, fecha_accion';
     $value.=", $struct, $_SESSION[userid], now()";
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print json_encode(update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")"));
  }
  elseif($campo == 'id_chofer_2'){
     $struct = STRUCTURED;
     if ($value == 0){
        $value = 'NULL';
        $struct = 'NULL';
     }
     $campo.=', id_estructura_chofer2, id_user, fecha_accion';
     $value.=", $struct, $_SESSION[userid], now()";
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print json_encode(update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")"));
  }
  elseif($campo == 'id_chofer_3'){
     $orden = $data[2];
     $struct = STRUCTURED;
     if ($id){
        if ($value){
           ejecutarSQL("REPLACE INTO tripulacionXOrdenes VALUES ($id, $orden, $struct, $value)");
        }
        else{
             ejecutarSQL("DELETE FROM tripulacionXOrdenes WHERE id = $id");
        }
     }
     else
         ejecutarSQL("INSERT INTO tripulacionXOrdenes (id_orden, id_estructura_orden, id_empleado) VALUES ($orden, $struct, $value)");
  }
  else{
       backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
       $campo.=", id_user, fecha_accion";
       $value.=", $_SESSION[userid], now()";
       print json_encode(update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")"));
  }
  //mysql_query($query, $conn)or die(mysql_error($conn));
 // mysql_close($conn);
?>
