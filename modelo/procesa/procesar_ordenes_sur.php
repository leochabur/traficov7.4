<?php
  session_start();
    error_reporting(0);
  include ($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include ($_SERVER['DOCUMENT_ROOT'].'/modelo/utils/dateutils.php');
  define('STRUCTURED', $_SESSION['structure']);
  
  $accion = $_POST['accion'];
  if ($accion == 'soe'){ //codigo para guarar una orden de un servicio en la BD

        $conn = conexcion();
        try{
                    
              $estructura = STRUCTURED;

              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";
              
              $nombre  = $_POST['nombre'];              
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=", 520, $estructura";

              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $campos.=",hcitacion";
              $values.=",'00:00'";

              $campos.=",hsalida";
              $values.=",'00:00'";
              
              $campos.=",hllegada";
              $values.=",'00:00'";
              
              $campos.=",hfinservicio, vacio";
              $values.=",'00:00', 1";                
      
              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $cleinteVacio = $_POST['corresponde_a'];
              $campos.=",id_cliente_vacio, id_estructura_cliente_vacio";
              $values.=",$cleinteVacio, $estructura";
              
              $id = insert('ordenes', "id, $campos", $values, $conn);

              $hcitacion = DateTime::createFromFormat('d/m/Y H:i', $_POST['hcitacion']);
              $hcitacion = $hcitacion->format('Y-m-d H:i:s');
              $values="'$hcitacion'";

              $hsalida = DateTime::createFromFormat('d/m/Y H:i', $_POST['hsalida']);
              $hsalida = $hsalida->format('Y-m-d H:i:s');
              $values.=",'$hsalida'";

              $hllegada = DateTime::createFromFormat('d/m/Y H:i', $_POST['hllegada']);
              $hllegada = $hllegada->format('Y-m-d H:i:s');
              $values.=",'$hllegada'";

              $hfinserv = DateTime::createFromFormat('d/m/Y H:i', $_POST['hfins']);
              $hfinserv = $hfinserv->format('Y-m-d H:i:s');
              $values.=",'$hfinserv'";

              insert("horarios_ordenes_sur", 
                    "id, id_orden, citacion, salida, llegada, finalizacion, id_estructura_orden, citacion_real, salida_real, llegada_real, finalizacion_real", 
                    "$id, $values, $estructura, $values",
                    $conn);
              commit($conn);
              @mysql_close($conn);
              print (json_encode(array("status" => true)));

          }    catch (Exception $e) {
                                   rollback($conn);
                                   mysql_close($conn);            
                                   print (json_encode(array("status" => false, 'sql'=>$e->getMessage())));
                                   }

  }
?>
