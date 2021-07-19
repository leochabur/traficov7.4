<?php
  set_time_limit(0);
  session_start();

  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
  $accion = $_POST['accion'];
  $estado = $_POST['estados'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
        $ok=1;
        $fecha = dateToMysql($_POST['fecha'], '/');
        $conn = conexcion();

        
		$sql="DELETE FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estructura = $_SESSION[structure])";
		$resul = mysql_query($sql, $conn);

        $sql = "update ordenes set hcitacionreal = hcitacion, hsalidaplantareal = hsalida, hllegadaplantareal = hllegada, hfinservicioreal = hfinservicio WHERE (fservicio ='$fecha') and (id_estructura = $_SESSION[structure])";
        $result = mysql_query($sql, $conn);
        
        $sql="INSERT INTO estadoDiagramasDiarios (id_estado, fecha, finalizado, usuario, fechahorafinalizacion, id_estructura) values ($_POST[estados], '$fecha', $estado, $_SESSION[userid] , now(), $_SESSION[structure])";
        $resul = mysql_query($sql, $conn);
        mysql_close($conn);
        print json_encode($ok);
  }
?>

