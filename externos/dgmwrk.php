<?php
	session_start();
      	if(!$_SESSION["auth"]) header("Location: ./index.php?e=true");
	include('main.php');
	include_once('../controlador/bdadmin.php');
	include_once('../controlador/ejecutar_sql.php');
	include_once('../vista/paneles/viewpanel.php');
?>
<style type="text/css">
thead tr th {padding:20px 10px 20px 10px ; }
tbody tr td {padding:3px 3px 3px 3px ; }
h2 {display:inline}

</style>

<script type="text/javascript">
                          function pulse() { 
                                            $('.blink').fadeIn(500); 
                                            $('.blink').fadeOut(500); 
                                          } 
                          $('.blink').hide();
                         // setInterval(pulse, 1000); 
</script>

<body vlink=#999999 text-decoration: none; >


<?php
     $inicio = date('d/m/Y');
     $fin = date('d/m/Y', mktime(0, 0, 0, date("m")  , date("d")+1, date("Y")));
	encabezado($_SESSION["chofer"],$_SESSION["nivel"]);

   /*             $conn = conexcion();
        $query = "SELECT upper(mensaje) as mensaje
		  FROM mensajes
		  WHERE (id_empleado = $_SESSION[id_chofer]) and (vigencia_desde <= date(now())) and (not visto)";
        $result = mysql_query($query, $conn);
        if (mysql_num_rows($result)){
        	$mje = "<fieldset class=\"el08\">
		        <legend>Mensajes pendientes</legend>";
                while ($data = mysql_fetch_array($result)){
                	$mje.="<div class=\"el08\" align=\"center\">$data[mensaje]<br></div>";
                }
		$mje.="</fieldset>
                </p>";
         	print $mje;
           }      */
?>


</head>

<body>

<?php
         menu();
         
if ($_SESSION[super]){ ?>
<link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.ui.datepicker-es.js"></script>
   <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Diagrama de Trabajo</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar por:</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Titular Servicios</td>
                                    <td>
                                    <select id="cond" name="cond" class="ui-widget ui-widget-content  ui-corner-all">
                                            <option value="0">Todos</option>
                                                <?php
                                                     $sql="SELECT id, upper(razon_social)
                                                           FROM empleadores e
                                                           where id in ($_SESSION[fleteros])
                                                           order by razon_social";
                                                     $result = ejecutarSQL($sql);
                                                     while ($row = mysql_fetch_array($result)){
                                                           print "<option value='$row[0]'>$row[1]</option>";
                                                     }
                                                ?>
                                        </select>
                                    </td>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    <td>
                                        <input type="button" value="Cargar Diagrama" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data">
                              <?//include_once("../../../modelo/informes/trafico/diagdia.php");?>
                         </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type='hidden' id='posy' name='posy'>
         </form>
         
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('select').selectmenu({width: 350});
                                                       $('#desde,#hasta').datepicker({dateFormat:'yy-mm-dd'});
                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $("#upuda").serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../vista/ajax-loader.gif' /></div>");
                                                                                              $.post("./diagdia.php", datos, function(data){$('#data').html(data);});
                                                       });

                          });
</script>
         
<?php    }
else{

$conn = conexcion();


$query="SELECT fservicio as fecha, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacionreal, '%H:%i') as hcitacionreal, 
              date_format(hsalidaplantareal, '%H:%i') as hsalidaplantareal, date_format(hfinservicioreal, '%H:%i') as hfinservicioreal, 
              o.nombre, upper(c.razon_social) as razon_social, comentario, interno, date_format(hcitacion, '%H:%i') as hcitacion, 
                date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinservicio
        FROM ordenes o
        inner JOIN empleados ch ON ((ch.id_empleado = o.id_chofer_1) or (ch.id_empleado = o.id_chofer_2)) and (ch.id_empleado = $_SESSION[id_chofer]) and (ch.id_estructura = o.id_estructura)
        LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
        LEFT JOIN unidades m ON (m.id = o.id_micro)
        WHERE (fservicio between curdate() and ADDDATE(curdate(),2)) and (not borrada) and (not suspendida)
        UNION
        SELECT date(curdate()) as fecha, date_format(curdate(), '%d/%m/%Y') as fservicio, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, nov_text, '' as razon_social, '' as comentario, '' as interno, '00:00', '00:00', '00:00'
        FROM novedades n
        inner join cod_novedades cn on cn.id = n.id_novedad
        where (date(curdate()) between desde and hasta) and (n.activa) and (n.id_empleado = $_SESSION[id_chofer])
        UNION
        SELECT date(ADDDATE(curdate(),1)) as fecha, date_format(ADDDATE(curdate(),1), '%d/%m/%Y') as fservicio, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, nov_text, '' as razon_social, '' as comentario, '' as interno, '00:00', '00:00', '00:00'
        FROM novedades n
        inner join cod_novedades cn on cn.id = n.id_novedad
        where (date(ADDDATE(curdate(),1)) between desde and hasta) and (n.activa) and (n.id_empleado = $_SESSION[id_chofer])
        UNION
        SELECT date(ADDDATE(curdate(),2)) as fecha, date_format(date(ADDDATE(curdate(),2)), '%d/%m/%Y') as fservicio, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, nov_text, '' as razon_social, '' as comentario, '' as interno, '00:00', '00:00', '00:00'
        FROM novedades n
        inner join cod_novedades cn on cn.id = n.id_novedad
        where (date(ADDDATE(curdate(),2)) between desde and hasta) and (n.activa) and (n.id_empleado = $_SESSION[id_chofer]) and (n.activa)
        order by fecha, hcitacion";

$result= mysql_query($query, $conn);
/*print "<hr>
        <center>
        <h2><a href='https://docs.google.com/forms/d/e/1FAIpQLScI6tmfP-u2TlA4V9LwIHr8ivw3FzypRK_fhYGIcS3rYq2CSw/viewform' target='_blanck'><font class='blink' color='red'>Encuesta de satisfaccion al personal - Ingresa aqui...</font></a></h2></center>";*/
if(mysql_num_rows($result) == 0){
    echo "<script> alert('No hay datos para mostrar')</script>";
}
else
{
    print "<HR>";
    print "<br><center>Diagrama de trabajo desde el <h2>$inicio</h2> al <h2>$fin </h2></center><br>";
	print"<center><table border='0px' class='ui-widget ui-widget-content' width='75%'>";
	$cliente=$data['nombrecliente'];
	$origen=$data['localidad'];
				print"<thead>
		                         <tr class='ui-widget-header'>
                                     <th>Fecha servicio</th>
    			                     <th>Hora Citacion</th>
    			                     <th>Hora Servicio</th>
    			                     <th>Cliente</th>
    			                     <th>Servicio</th>
    			                     <th>Interno</th>
                                 </tr>
                     </thead>
                     <tbody>";



    $data= mysql_fetch_array($result);
    $i=0;
    $ref=0;
    $est_diag="SELECT ed.estado, e.id_estado
               FROM estadoDiagramasDiarios e, estadosDiagrama ed
               where (e.id_estado = ed.id) and (e.fecha between curdate() and ADDDATE(curdate(),2))";

    while($data){
         $ult = $data['fservicio'];
         $est_diag="SELECT *
                    FROM estadoDiagramasDiarios e
                    where (fecha = '$data[fecha]') and (id_estado = 1) and (e.id_estructura = 1)";

         $result_est_diag= mysql_query($est_diag, $conn);
         if (mysql_num_rows($result_est_diag)){

            if (($i%2)==0){
               $color="#808080";
            }
            else{
                 $color="#D0D0D0";
            }
         }
         else{
              $ref=1;
              $color="#FF0000";
         }
         while(($data) && ($ult == $data['fservicio'])){
              if (mysql_num_rows($result_est_diag))
              {
                 $hcitacion=$data['hcitacionreal'];
    			       $hsalida=$data['hsalidaplantareal'];
              }
              else{
                 $hcitacion=$data['hcitacion'];
                 $hsalida=$data['hsalida'];
              }
			       $fecha = $data['fservicio'];


            print"<tr bgcolor=\"$color\">";



			//print "<td><div align=\"center\">". $data['fservicio'] . "&nbsp;&nbsp;$hcitacion</div></td>
			print "
                <td bordercolor=\"$color\"><div align=\"center\">$fecha</div></td>
                <td bordercolor=\"$color\"><div align=\"center\">$hcitacion</div></td>
                <td><div align=\"center\">$hsalida</div></td>
				<td>$data[razon_social]</td>
    			<td><div align=\"rigth\">".ucwords(strtolower(htmlentities($data['nombre'])))."</div></td>
    			<td> $data[interno] </td>";
            $data= mysql_fetch_array($result);
          }
          $i++;
	}
	mysql_free_result($result);
	print"</td>
	</tr>
	<tbody>
	</table>
    </center>
    <br>";

    if($ref){
       print "<table width=\"25%\">
                  <tr bgcolor=\"#FF0000\">
                     <td ><b>Diagrama sujeto a modificaciones</b></td>
                 </tr>
             </table>";
    }

}
}

?>
  <p align="right">
  
  <br>
</p>
  <hr id="hr">


<p class="tmediano">
  <?php
  if(isset($conn)){
	mysql_close($conn);
}

?>


</p>
</body>
</html>
