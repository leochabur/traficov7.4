<?php

	session_start();
        if (!(isset($_SESSION['auth']) && ($_SESSION['auth'] == true)))
           echo "<script>window.location='index.php'</script>";
        else{
            include('data.php');
            }

?>
<style type="text/css">
table {border-collapse:collapse;border:1px solid maroon;margin-left:0px}
td {border:1px solid maroon;width:30px;text-align:center}
fieldset { border:2px solid red;
	   font-size:2em;}

legend {
  padding: 0.2em 0.5em;
  border:2px solid red;
  color:red;
  font-size:90%;
  text-align:right;
  }
</style>
<script type="text/javascript">
<!--
function ini() {
  tab=document.getElementById('tabla');
  for (i=0; ele=tab.getElementsByTagName('td')[i]; i++) {
    ele.onmouseover = function() {iluminar(this,true)}
    ele.onmouseout = function() {iluminar(this,false)}
  }
}

function iluminar(obj,valor) {
  fila = obj.parentNode;

    for (i=0; ele = fila.getElementsByTagName('td')[i]; i++)
      ele.style.background = (valor) ? '#B9F8F8' : '';
}
-->
</script>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php encabezado($_SESSION["chofer"], '');?>
<br>
<br>
<div align="left"><span class="tgrande"><span class="celeste"> &gt; </span><?echo $_SESSION["chofer"]?></span></div>
<br>
<?php
                $conn = conectar_mysql();
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
           }
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilo.css" rel="stylesheet" type="text/css">
<link href="estilo2.css" rel="stylesheet" type="text/css">
<!--<link href="jtip.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/trafico.js"></script>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="jquery.jeditable.js"></script>
<script type="text/javascript" src="jquery.traf.js"></script>
<script type="text/javascript" src="jtip.js"></script-->

</head>

<body vlink=#999999>

<?php

$mysqli = new mysqli('trafico.masterbus.net', 'masterbus', 'master,07a', 'trafico');
if (mysqli_connect_error()) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}
else{
$query="SELECT fservicio as fechaserv, date_format(fservicio,'%d/%m/%y') as fservicio, hcitacion, hsalida, nombrecliente, localidad, nombre, interno, id_orden
               FROM ordenes a
               INNER JOIN servicios ON a.id_servicio=servicios.id_servicio
               INNER JOIN cronogramas ON cronogramas.codigo=servicios.cod_cronograma
               INNER JOIN micros ON micros.id_micro=a.id_micro
               INNER JOIN choferes ch ON (ch.id_chofer=a.id_chofer1) or (ch.id_chofer = a.id_chofer2)
               INNER JOIN localidades ON localidades.id_localidad=cronogramas.origen
               where (ch.legajo = $_SESSION[legajo]) and (fservicio between curdate() and ADDDATE(curdate(),2))
               order by fservicio,hcitacion, hsalida, nombrecliente, localidad, codigo";

$result= mysqli_query($mysqli,$query);
if(mysqli_num_rows($result) == 0){
    echo "<script> alert('No hay datos para mostrar')</script>";
}
else
{
    $res = mysqli_fetch_array(mysqli_query($mysqli, "SELECT date_format(curdate(),'%d/%m/%y') as inicio, date_format(ADDDATE(CURDATE(),2),'%d/%m/%y') as fin"));
    print "<HR>";
    print "<br><center><h3>Diagrama de trabajo desde el <span class=\"tmediano\"> $res[inicio] </span> al <span class=\"tmediano\"> $res[fin] </span></h3></center>";
	print"<center><table width=\"100%\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\"  id=\"tabla\">";
	$cliente=$data['nombrecliente'];
	$origen=$data['localidad'];
				print"
				<tr bgcolor=\"#0099FF\">
                <td ><span class=\"Estilo3\"><div align=\"center\">Fecha servicio</div></span></td>
    			<td ><span class=\"Estilo3\"><div align=\"center\">Hora Citacion</div></span></td>
    			<td ><span class=\"Estilo3\"><div align=\"center\">Hora Servicio</div></span></td>
    			<td ><span class=\"Estilo3\"><div align=\"center\">Cliente</div></span></td>
    			<td ><span class=\"Estilo3\"><div align=\"center\">Servicio</div></span></td>
    			<td ><span class=\"Estilo3\"><div align=\"center\">Interno</div></span></td>";



    $data= mysqli_fetch_array($result);
    $i=0;
    $ref='';
    while($data)
	{
         $ult = $data['fservicio'];
         $est_diag="SELECT ed.estado, e.finalizado
                        FROM estadoDiagramasDiarios e, estadosDiagrama ed
                        where (e.id_estado = ed.id) and (e.fecha = '$data[fechaserv]')";
         $result_est_diag= mysqli_fetch_array(mysqli_query($mysqli,$est_diag));
         if ($result_est_diag['finalizado']){

            if (($i%2)==0){
               $color="#A8A8A8";
            }
            else{
                 $color="#FFFFFF";
            }
         }
         else{
              $ref=1;
              $color="#FF0000";
         }
         while(($data) && ($ult == $data['fservicio'])){
            $hcitacion=substr($data['hcitacion'],0,5);
			$hsalida=substr($data['hsalida'],0,5);
			$id_orden=$data[id_orden];
			$finalizada=$data['finalizada'];
			$chequeada=$data['chequeada'];
			$fecha = $data['fservicio'];
			$chofer2 = $listachoferes[ $data['id_chofer2'] ];

            print"<tr bgcolor=\"$color\">";



			//print "<td><div align=\"center\">". $data['fservicio'] . "&nbsp;&nbsp;$hcitacion</div></td>
			print "
                <td><div align=\"center\">$fecha</div></td>
                <td><div align=\"center\">$hcitacion</div></td>
                <td><div align=\"center\">$hsalida</div></td>
				<td>$data[nombrecliente]</td>";
				print "
    			<td><div align=\"center\">$data[nombre]</div></td>
    			<td> $data[interno] </td>";
            $data= mysqli_fetch_array($result);
          }
          $i++;
	}
	mysqli_free_result($result);
	print"</td>
	</tr>
	</table>
    </center>
    <br>";
    
    if($ref){
       print "<table width=\"15%\">
                  <tr bgcolor=\"#FF0000\">
                     <td >Diagrama sujeto a modificaciones</td>
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
  if(isset($mysqli)){
	mysqli_close($mysqli) or die(alerta(mysql_error()));
}

piepagina($_SESSION["nivel"]); ?>
</p>
</body>
</html>
