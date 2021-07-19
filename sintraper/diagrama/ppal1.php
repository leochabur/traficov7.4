<?php
/*-----------------------------------------------------------------------------------------------------------------*/
/*                              M O D U L O  -  P A N T A L L A  P R I N C I P A L  .INGRESAR                      */
/*-----------------------------------------------------------------------------------------------------------------*/
	session_start();
      	if(!$_SESSION["auth"]) header("Location: ./index.php?e=true");
	include('main.php');
	include_once('../controlador/bdadmin.php');
?>


<body vlink=#999999 text-decoration: none; >


<?php

	encabezado($_SESSION["chofer"],$_SESSION["nivel"]);


         /*
        $link= conexcion();
        $query="SELECT upper(l.licencia) as licencia, date_format(max(lc.vigencia_hasta), '%d/%m/%Y') as vto, datediff(max(vigencia_hasta), date(now())) as dias
               FROM empleados e
               inner join licenciaconductor lc on lc.id_conductor = e.id_empleado
               inner join licencias l on l.id = lc.id_licencia
               WHERE (e.id_empleado = $_SESSION[id_chofer])
               group by lc.id_conductor, lc.id_licencia";
        $result=mysql_query($query, $link);
        if (mysql_num_rows($result) > 0){
           while ($data= mysql_fetch_array($result)){
                 if ($data['dias'] <= 0){
                    alerta("Su licencia $data[licencia] venció el $data[vto]");
                 }
                 else{
                      if (($data['dias'] <= 30) && ($data['dias'] > 0)){
                         aviso("Su licencia $data[licencia] vence dentro de $data[dias] dias. El $data[vto]");
                      }
                 }
           }
        }
        @mysql_close($link);   */
        menu();
        $conn = conexcion();
        $query = "SELECT id, upper(mensaje) as mensaje, visto
		          FROM mensajes
		          WHERE (id_empleado = $_SESSION[id_chofer]) and (vigencia_desde <= date(now())) and (visto < 2)";
        $result = mysql_query($query, $conn);
        if (mysql_num_rows($result)){
        	$mje = "<br><fieldset class='ui-widget ui-widget-content ui-corner-all'>
		        <legend class='ui-widget ui-widget-header ui-corner-all'>Mensajes pendientes</legend>";
                while ($data = mysql_fetch_array($result)){
                	$mje.="<div class=\"el08\" align=\"center\"><b>$data[mensaje]</b><br></div>";
                	if ((isset($_GET['pv'])) && ($_GET['pv'] == 's')){
                       $campo_vto = "fecha_vista_1";
                       if ($data['visto'] == 1)
                          $campo_vto = "fecha_vista_2";
                       $vto=$data['visto'] + 1;
                       $upd = "update mensajes set visto = $vto, $campo_vto = now() where id = $data[id]";
                       mysql_query($upd);
                	}
                }
	        $mje.="</fieldset>
                </p>";
         	print $mje;
          }
          mysql_close($conn);

  ?>
</p>
</body>
</html>
