<?php
/*-----------------------------------------------------------------------------------------------------------------*/
/*                              M O D U L O  -  P A N T A L L A  P R I N C I P A L  .INGRESAR                      */
/*-----------------------------------------------------------------------------------------------------------------*/
	session_start();
      	if(!$_SESSION["auth"]) header("Location: ./index.php?e=true");
	include('../externos/main.php');
	include_once('../controlador/bdadmin.php');
?>


<body vlink=#999999 text-decoration: none; >
<style>

	#calendar {
		width: 400px;
		margin: 0 auto;
		}
.serif {
  font-family: "Times New Roman", Times, serif;
  font-size:20px;
  line-height: 170%;
  margin-right: 50px;
  margin-left: 50px;
}

</style>

<?php

	encabezado($_SESSION["chofer"],$_SESSION["nivel"]);
?>
<link href='./fullcalendar-1.6.4/fullcalendar/fullcalendar.css' rel='stylesheet' />
<link href='./fullcalendar-1.6.4/fullcalendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='./fullcalendar-1.6.4/fullcalendar/fullcalendar.min.js'></script>
<script type="text/javascript">
                          function pulse() { 
                                            $('.blink').fadeIn(500); 
                                            $('.blink').fadeOut(500); 
                                          } 
                          setInterval(pulse, 1000); 
</script>
<?php


        menu();
          print "<br><p class='serif' align='justify'>
                  A partir del <b>MARTES 13 de Octubre de 2020</b> todos los rondines van a estar pasando 10 minutos antes de lo habitual.
                  Los nuevos horarios estaran publicados en la pagina oficial de recorridos a partir del <b>SABADO 10 de Octubre de 2020</b>
                  </p>";
     if ((!$_SESSION['super']) && ($_SESSION['id_empleador'] == 51))
     {
        $conn = conexcion();
          print "<br>
          <hr>
                <div align='center'><h2>FRANCOS DIAGRAMADOS A PARTIR DE LA FECHA</h2></div>
                <div id='calendar'></div>";
          $sql="SELECT nov_text, year(desde), month(desde)-1, day(desde), year(hasta), month(hasta)-1, day(hasta)
                from novedades n
                inner join cod_novedades cn on cn.id = n.id_novedad
                where (n.id_empleado = $_SESSION[id_chofer])and (n.activa)";
               // die($sql);
          $result = mysql_query($sql, $conn);
          
          print "<script>
                 $('#calendar').fullCalendar({
		                                      monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
                                              monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
                                              dayNames: ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'],
                                              dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
			                                  header: {
				                                      left: 'prev',
				                                      center: 'title',
				                                      right: 'next'
                                              },
			                                  editable: false,
			                                  events: [";
          $i = mysql_num_rows($result);
          while ($data = mysql_fetch_array($result)){
                $i--;
                print "{
					    title: '".htmlentities($data[0])."',
					    start: new Date($data[1], $data[2], $data[3]),
					    end: new Date($data[4], $data[5], $data[6]),
					    allDay: true
				       }";
                 if ($i > 0)
                    print ",";
          
          }

		print"	]
		});


</script>";
          
          
          
          mysql_close($conn); }
 print "<br>";
 
  ?>
</p>
</body>
</html>
