<?php
	session_start();
      	if(!$_SESSION["auth"]) header("Location: ./index.php?e=true");
	include('main.php');
	include_once('../controlador/bdadmin.php');
?>
<style type="text/css">
thead tr th {padding:20px 10px 20px 10px ; }
tbody tr td {padding:3px 3px 3px 3px ; }
h2 {display:inline}
</style>
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
$conn = conexcion();

$query="SELECT fservicio as fecha, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfinserv, o.nombre, upper(c.razon_social) as razon_social, comentario, interno
        FROM ordenes o
        inner JOIN empleados ch ON ((ch.id_empleado = o.id_chofer_1) or (ch.id_empleado = o.id_chofer_2)) and (ch.id_empleado = $_SESSION[id_chofer]) and (ch.id_estructura = o.id_estructura)
        LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
        LEFT JOIN unidades m ON (m.id = o.id_micro)
        WHERE (fservicio between curdate() and ADDDATE(curdate(),2)) and (not borrada)
        UNION
        SELECT date(curdate()) as fecha, date_format(curdate(), '%d/%m/%Y') as fservicio, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, nov_text, '' as razon_social, '' as comentario, '' as interno
        FROM novedades n
        inner join cod_novedades cn on cn.id = n.id_novedad
        where (date(curdate()) between desde and hasta) and (n.id_empleado = $_SESSION[id_chofer])
        UNION
        SELECT date(ADDDATE(curdate(),1)) as fecha, date_format(ADDDATE(curdate(),1), '%d/%m/%Y') as fservicio, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, nov_text, '' as razon_social, '' as comentario, '' as interno
        FROM novedades n
        inner join cod_novedades cn on cn.id = n.id_novedad
        where (date(ADDDATE(curdate(),1)) between desde and hasta) and (n.id_empleado = $_SESSION[id_chofer])
        UNION
        SELECT date(ADDDATE(curdate(),2)) as fecha, date_format(date(ADDDATE(curdate(),2)), '%d/%m/%Y') as fservicio, '00:00' as hcitacion, '00:00' as hsalida, '00:00' as hfinserv, nov_text, '' as razon_social, '' as comentario, '' as interno
        FROM novedades n
        inner join cod_novedades cn on cn.id = n.id_novedad
        where (date(ADDDATE(curdate(),2)) between desde and hasta) and (n.id_empleado = $_SESSION[id_chofer])
        order by fecha, hcitacion";


$result= mysql_query($query, $conn);
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
               where (e.id_estado = ed.id) and (e.fecha between curdate() and ADDDATE(curdate(),2)) and (id_estructura in (SELECT id_estructura FROM empleados where id_empleado = $_SESSION[id_chofer]))";

    while($data){
         $ult = $data['fservicio'];
         $est_diag="SELECT *
                    FROM estadoDiagramasDiarios e
                    where (fecha = '$data[fecha]') and (id_estado = 1) and (id_estructura in (SELECT id_estructura FROM empleados where id_empleado = $_SESSION[id_chofer]))";

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
            $hcitacion=$data['hcitacion'];
			$hsalida=$data['hsalida'];

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
