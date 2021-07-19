<?php
  session_start();
  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
  $accion = $_POST['accion'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
        $fecha = dateToMysql($_POST['fecha'], '/');
        $conn = conexcion();
        
		$sql="SELECT * FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estructura = $_SESSION[structure])";
		$resul = mysql_query($sql, $conn);
		$ok=1;

        
		$sql="DELETE FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estructura = $_SESSION[structure])";
		$resul = mysql_query($sql, $conn);
		$estado=0;
	/*	if ($_POST['estados'] == 1){
		   $estado=1;
		   $sql_mod_nov="select id
                         from (select id, id_novedad, id_empleado from novedades where '$fecha' between desde and hasta and id_novedad = 40) n
                         inner join (select id_chofer_1 from ordenes where (fservicio = '$fecha') and (id_estructura = $_SESSION[structure])) o on o.id_chofer_1 = n.id_empleado
                         group by id
                         union all
                         select id
                         from (select id, id_novedad, id_empleado from novedades where '$fecha' between desde and hasta and id_novedad = 40) n
                         inner join (select id_chofer_2 from ordenes where (fservicio = '$fecha') and (id_estructura = $_SESSION[structure])) o on o.id_chofer_2 = n.id_empleado
                         group by id";
           $result = mysql_query($sql_mod_nov);
           while ($data = mysql_fetch_array($result)){
                 $mod_nov = "UPDATE novedades SET id_novedad = 18 WHERE id = $data[0]";
                 mysql_query($mod_nov, $conn);
           }
        }    */

        $exists = mysql_query("SELECT * FROM horarios_ordenes WHERE fservicio = '$fecha' limit 1", $conn);
        if (!mysql_fetch_array($exists)){
           $sql = "SELECT id FROM ordenes WHERE fservicio = '$fecha' and id_estructura = $_SESSION[structure]";
           $result = mysql_query($sql, $conn);
           while ($data = mysql_fetch_array($result)){
              mysql_query("INSERT INTO horarios_ordenes (SELECT * FROM ordenes WHERE id = $data[0])", $conn);
           }
        }
        $sql="INSERT INTO estadoDiagramasDiarios (id_estado, fecha, finalizado, usuario, fechahorafinalizacion, id_estructura) values ($_POST[estados], '$fecha', $estado, $_SESSION[userid] , now(), $_SESSION[structure])";
        $resul = mysql_query($sql, $conn);
        mysql_close($conn);
        print json_encode($ok);
  }
?>

