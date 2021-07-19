<?php
/*------------------------------------------------------------------------------------------------------- */
/*                           M O D U L O  -  A B M  -  O R D E N E S                                      */
/*------------------------------------------------------------------------------------------------------- */
/* Autor : Juan Girini <juangirini@yahoo.com.ar>                                                          */

	session_start();
	if($_SESSION["nivel"]<2) header("Location: ../index.php?e=true");
	include('../data.php');
	
	function eliminarOrden($conexcion, $numero){
             $result = mysql_query("SELECT * FROM ordenes WHERE id_orden = $numero", $conexcion);
             if ($data = mysql_fetch_array($result)){
                       $backup="INSERT INTO ordenesaux
                                VALUES('$data[id_orden]', '$data[id_servicio]', '$data[fcreacion]',
                                       '$data[fservicio]', '$data[ffin]', '$data[id_micro]', '$data[kmreales]',
                                       '$data[id_chofer1]', '$data[id_chofer2]', '$data[kmsalida]', '$data[kmregreso]',
                                       '$data[kminicial]', '$data[kmfinal]', '$data[hfinservreal]','$data[pasajeros]',
                                       '$data[nrolistapas]', '$data[excursion]', '$data[lugar]', '$data[video]', '$data[jugo]',
                                       '$data[cafe]', '$data[pelicula]', '$data[peajesac]', '$data[viaticosac]', '$data[alojamientoac]',
                                       '$data[estac]', '$data[valorviaje]', '$data[observaciones]', '$data[finalizada]',
                                       '$nombre_guardar')";
                       $resultBackup=mysql_query($backup, $conexcion);
                       if(mysql_affected_rows() > 0){
                            $query3="DELETE FROM ordenes WHERE id_orden=$numero";
                            $result3=mysql_query($query3, $conexcion);
                       }
             }
	}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php titulo($_SESSION["apenom"]);
$nombre_guardar=$_SESSION["apenom"];?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../estilo.css" rel="stylesheet" type="text/css">

<script language="javascript" type="text/javascript">

        function cambiar(id){
                     var valor = document.getElementById('actualizar').value
                     document.getElementById('actualizar').value = valor + "," + id

        }

function valida1(){
         alert(document.getElementById('actualizar').value)


}
</script>
<script>
var micros=new Array();
var choferes=new Array();
<?php
function alertPop($msj){
		print"	<script language=\"javascript\" type=\"text/javascript\">
					alert(\"$msj\");
				</script>";
}

$conn = conectar_mysql() or die(alerta(mysql_error())); //mysql_connect('localhost', 'masterbus', 'master,07a');// //CONECTAR
mysql_select_db('trafico', $conn);
                 $result = mysql_query('SELECT * FROM micros WHERE activo ORDER BY interno', $conn);
                 $i=0;
                 while ($data = mysql_fetch_array($result)){
                       ?>
                            micros.push(new Array(<?php echo $data['id_micro'];?>,  <?php echo $data['interno'];?>));
                       <?php
                       $i++;
                 }

                 $result = mysql_query("SELECT id_chofer, concat(legajo,' - ', apenom) as name FROM choferes WHERE activo ORDER BY apenom", $conn);

                 $i=0;
                 while ($data = mysql_fetch_array($result)){
                       ?>
                            choferes.push(new Array(<?php echo $data['id_chofer'];?>,  '<?php echo  "$data[name]";?>'));
                       <?php
                       $i++;
                 }
?>
        function cargarComboMicros(obj){
                        var i;
                        if (obj.options.length < 5)
                        for (i = 1; i <= micros.length; i++){
                            var elOptNew = document.createElement('option');
                            elOptNew.text = micros[i-1][1];
                            elOptNew.value = micros[i-1][0];
                            obj.options.add(elOptNew, i);
                        }
        }

        function cargarComboChoferes(obj){
                        var i;
                        if (obj.options.length < 5)
                        for (i = 1; i <= choferes.length; i++){
                            var elOptNew = document.createElement('option');
                            elOptNew.text = choferes[i-1][1];
                            elOptNew.value = choferes[i-1][0];
                            obj.options.add(elOptNew, i);
                        }
        }
</script>
<style type="text/css">
<!--
.Estilo2 {	color: #000000;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-weight: bold;

}

.Estilo1 {
	font-size: 18px;
	font-weight: bold;
	font-family:Verdana, Arial, Helvetica, sans-serif;
}
.Estilo3 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	color: #333333;
	font-weight: bold;
	font-size:11px;
}
.Estilo5 {color: #FFFFFF}
-->
</style>

</head>
 <?php
      if (isset($_POST['boton'])){
         $ordenesModificadas = 0;
         $ordenesEliminadas = 0;
         $ordenesFinalizadas = 0;
         $ordenes = explode(',', $_POST['actual']);
         $ordenes = array_unique($ordenes);
         foreach ($ordenes as $numero){
                 $km = $_POST["km$numero"];
                 $micro = $_POST["micro$numero"];
                 $chofer = $_POST["chofer$numero"];
                 if ($numero){//pregunta porque el primer elemento del arreglo siempre es un valor en blanco
                     $query="UPDATE ordenes SET id_micro = $micro, kmreales = $km,
                             id_chofer1 = $chofer, responsable='$_SESSION[apenom]' WHERE id_orden =$numero";
                     die($query."    "."chofer$numero"."  ".$_POST["chofer$numero"]);
                     mysql_query($query, $conn);
                     if (mysql_affected_rows($conn) > 0){
                        $ordenMod = "INSERT INTO ordenes_modificadas (select * from ordenes where id_orden = $numero), now()";
                        mysql_query($ordenMod, $conn);
                        $ordenesModificadas++;
                     }
                     if (isset($_POST["elim$numero"])){
                        eliminarOrden($conn, $numero);
                        $ordenesEliminadas++;
                     }
                     if (isset($_POST["fina$numero"])){
                        $finaOrden = "UPDATE ordenes set finalizada = 1 WHERE id_orden = $numero";
                        mysql_query($finaOrden, $conn);
                        $ordenesFinalizadas++;
                     }
                 }
         }
         aviso("Se modificaron $ordenesModificadas ordenes");
         aviso("Se finalizaron $ordenesFinalizadas ordenes");
         aviso("Se desactivaron $ordenesEliminadas ordenes");
      }
 ?>
<body>
<?php
//OBTENER DIA DE LA SEMANA
function getweekday($m,$d,$y)
{
   $tstamp=mktime(0,0,0,$m,$d,$y);

   $Tdate = getdate($tstamp);

   $wday=$Tdate["wday"];

   switch($wday)
   {
       case 0;
       $day="Domingo";
       break;

       case 1;
       $day="Lunes";
       break;


       case 2;
       $day="Martes";
       break;

       case 3;
       $day="Miercoles";
       break;

       case 4;
       $day="Jueves";
       break;

       case 5;
       $day="Viernes";
       break;

       case 6;
       $day="Sabado";
       break;
   }

     return $day;

}
//FIN OBTENER DIA DE LA SEMANA
encabezado($_SESSION["apenom"],$_SESSION["nivel"]);
sscanf($_GET[f],"%d-%d-%d",$ano,$m,$d);
$dia=getweekday($m,$d,$ano);
$anores=substr($ano,2,2);
if($_GET['t']=='s') $t="fservicio";
	else $t="fcreacion";
print"<hr><span class=\"Estilo2\">$dia $d/$m/$anores</span>";
?>

<form method="POST" action="resmodificar.php?f=<?php echo $_GET[f];?>&t=<?php echo $_GET[t]?>">

<div align="right"><input type="submit" name="boton" value="Modificar Ordenes"></div>
<input type="hidden" id="actualizar" name="actual" value="">
<?php

$query="select id_orden, finalizada, c.nombreCliente, s.referencia, localidad, c.nombre, c.codigo, s.hsalida, ch.id_chofer, ch.apenom, m.id_micro, m.interno, kmreales
from (select id_orden, id_servicio, finalizada, id_micro, id_chofer1, kmreales from ordenes where $t = '$_GET[f]') o
inner join servicios s on s.id_servicio = o.id_servicio
inner join cronogramas c on c.codigo = s.cod_cronograma
inner join localidades l ON l.id_localidad = c.origen
left join micros m on m.id_micro = o.id_micro
left join choferes ch on ch.id_chofer = o.id_chofer1
where nombreCliente >= 'P'
ORDER BY nombrecliente, localidad, codigo, hsalida";
$cliente="";
$origen="";
$result=mysql_query($query, $conn) or die(alerta(mysql_error()));
	print"<table width=\"100%\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"  >";
	while($data=mysql_fetch_array($result))
	{
			$hsalida=substr($data['hsalida'],0,5);
			$id_orden=$data[id_orden];
			$finalizada=$data['finalizada'];
			if($data['nombreCliente']<>$cliente){
			//IMPRIME ENCABEZADO
				$cliente=$data['nombreCliente'];
				$origen=$data['localidad'];
				print"
				<tr><td colspan=\"10\"><hr></td></tr>
				<tr bgcolor=\"#dddddd\" >
      			<td colspan=\"10\"><span class=\"Estilo1\"><div align=\"center\">$cliente</div></span></td>
				</tr>
				<tr><td colspan=\"10\"><hr></td></tr>
				<tr bgcolor=\"#dddddd\">
      			<td colspan=\"10\"  align=\"center\"><span class=\"Estilo2\">$origen</span></td>
    			</tr>
				<tr><td colspan=\"10\"><hr></td></tr>
				<tr>
    			<td width=\"10%\"><span class=\"Estilo3\"><div align=\"center\">Nro. órden</div></span></td>
    			<td width=\"12%\"><span class=\"Estilo3\"><div align=\"center\">Nom. Servicio</div></span></td>
    			<td width=\"12%\"><span class=\"Estilo3\"><div align=\"center\">Servicio</div></span></td>
    			<td width=\"12%\"><span class=\"Estilo3\"><div align=\"center\">Horario</div></span></td>
    			<td width=\"7%\"><span class=\"Estilo3\"><div align=\"center\">Km</div></span></td>
    			<td width=\"10%\"><span class=\"Estilo3\"><div align=\"center\">Int.</div></span></td>
    			<td width=\"16%\"><span class=\"Estilo3\"><div align=\"center\">Chofer</div></span></td>
				<td width=\"7%\" bgcolor=\"#ffcc99\"><span class=\"Estilo3\"><div align=\"center\">Desactivar</div></span></td>
				<td width=\"6%\" bgcolor=\"#ffff99\"><span class=\"Estilo3\"><div align=\"center\">Finalizar</div></span></td>
    			</tr>
				<tr><td colspan=\"10\"><hr></td></tr>";
			//FIN ENCABEZADO
			}
			else{
				if($data['localidad']<>$origen){
					//IMPRIME SUB ENCABEZADO
					$origen=$data['localidad'];
					print"
					<tr><td colspan=\"10\"><hr></td></tr>
					<tr bgcolor=\"#dddddd\" >
      				<td colspan=\"10\" align=\"center\"><span class=\"Estilo2\">$origen</span></td>
    				</tr>
					<tr><td colspan=\"10\"><hr></td></tr>
					<tr>
					<td width=\"10%\"><span class=\"Estilo3\"><div align=\"center\">Nro. órden</div></span></td>
					<td width=\"12%\"><span class=\"Estilo3\"><div align=\"center\">Nom. Servicio</div></span></td>
					<td width=\"12%\"><span class=\"Estilo3\"><div align=\"center\">Servicio</div></span></td>
					<td width=\"12%\"><span class=\"Estilo3\"><div align=\"center\">Horario</div></span></td>
					<td width=\"7%\"><span class=\"Estilo3\"><div align=\"center\">Km</div></span></td>
					<td width=\"10%\"><span class=\"Estilo3\"><div align=\"center\">Int.</div></span></td>
					<td width=\"16%\"><span class=\"Estilo3\"><div align=\"center\">Chofer</div></span></td>
					<td width=\"7%\" bgcolor=\"#ffcc99\"><span class=\"Estilo3\"><div align=\"center\">Desactivar</div></span></td>
					<td width=\"6%\" bgcolor=\"#ffff99\"><span class=\"Estilo3\"><div align=\"center\">Finalizar</div></span></td>
    				</tr>
					<tr><td colspan=\"10\"><hr></td></tr>";
					//FIN SUB ENCABEZADO
				}
			}
			//IMPRIME CUERPO DE LA TABLA
			$color=="#ffffff"?$color="#eeeeee":$color="#ffffff";
			if($finalizada==1) $color="#FFFF99";

			print"<tr bgcolor=\"$color\">
    			<td ><div align=\"center\"><a href=\"#\" onClick=\"javascript:window.open('modificar.php?cant=1&cond=id_orden=$data[id_orden]&maxc=1','','location=no,width=700,height=500,resizable=yes, scrollbars=yes')\">$data[id_orden]</a></div></td>
    			<td ><div align=\"center\">$data[referencia]</div></td>
    			<td><div align=\"center\">$data[nombre]</div></td>
    			<td><div align=\"center\">$hsalida</div></td>
    			<td><div align=\"center\"><input type=\"text\" name=\"km$id_orden\" value=\"$data[kmreales]\" size=\"7\" maxlength=\"7\" onchange=\"cambiar($id_orden)\"></div></td>
    			<td><select name=\"micro$id_orden\" align=\"center\" onFocus=\"cargarComboMicros(this)\" onchange=\"cambiar($id_orden)\">
				<option value=\"$data[id_micro]\"><div align=\"center\">$data[interno]</div></option>
                </select>";
			print"</td>
    			<td><select name=\"chofer$id_orden\" onFocus=\"cargarComboChoferes(this)\" onchange=\"cambiar($id_orden)\">
				<option value=\"$data[id_chofer]\"><div align=\"center\">$data[apenom]</div></option>
				</select>";
		//	if(($_SESSION["nivel"]>=3&&$finalizada!=1)||$_SESSION["nivel"]>=5){
				print"</td>
				<td bgcolor=\"#ffcc99\"> <div align=\"center\" ><input type=\"checkbox\" name=\"elim$id_orden\"></div></td>
				<td bgcolor=\"#ffff99\"> <div align=\"center\" ><input type=\"checkbox\" name=\"fina$id_orden\"></div></td>
    			</tr>";
		//	}
		/*	else {
				print"</td>
				<td>&nbsp;</td>
    			</tr>";
			}     */

	}


	mysql_free_result($result);
	print"</td>
	</tr>
				<tr><td colspan=\"10\"><hr></td></tr>
	</table>";

?>






</form>


  <p align="right">
  
  <br>
</p>
  <table id="amarillo"><tr><td bgcolor="#FFFF99">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td><td>órdenes finalizadas</td>
  </tr></table>
  <hr id="hr">
  <p class="tmediano" id="volver"><span class="celeste">&gt; </span><a href="./index.php">Volver al menú anterior</a></p>

<p class="tmediano">
  <?php
  if(isset($conn)){
	mysql_close($conn) or die(alerta(mysql_error()));
}

piepagina($_SESSION["nivel"]); ?>
</p>
</body>
</html>
