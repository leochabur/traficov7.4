<?
  session_start();
  include ('../../controlador/bdadmin.php');

  define(STRUCTURED, $_SESSION['structure']);
  
     $conn = conexcion();
     $cond = $_POST['conductor'];
     $desde = $_POST['desde'];
     $hasta = $_POST['hasta'];
     
     $sql = "SELECT o.id, finalizada, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, o.nombre, concat(ch1.apellido, ', ',ch1.nombre) as chofer1, upper(c.razon_social) as razon_social, concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno
             FROM ordenes o
             LEFT JOIN empleados ch1 ON ((ch1.id_empleado = o.id_chofer_1) and (ch1.id_estructura = o.id_estructura_chofer1))
             LEFT JOIN empleados ch2 ON ((ch2.id_empleado = o.id_chofer_2) and (ch2.id_estructura = o.id_estructura_chofer2))
             LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
             LEFT JOIN micros m ON (m.id_micro = o.id_micro) and (m.id_estructura = o.id_estructura_micro)
             WHERE (fservicio between '$desde' and '$hasta') and (o.id_estructura = ".STRUCTURED.") and ((ch1.id_empleado = $cond) or (ch2.id_empleado = $cond))";

     $resultado = mysql_query($sql, $conn) or die($sql.' - '.mysql_error($conn)); //Cierra la orden
     if(!$resultado) //flag para verificar la correcta modificacion
        $error=1;
        
     if($error) {      //si se produjo algun error realiza el rollback
             $sql = "ROLLBACK";
             mysql_query($sql, $conn);
     }
     else {
          $sql = "COMMIT";
          mysql_query($sql, $conn);
     }
     mysql_close($conn);
     print $error;
     

?>

