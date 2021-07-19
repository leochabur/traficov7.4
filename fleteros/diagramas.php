<script type="text/javascript" src="jquery-1.3.2.js"></script>
<script type="text/javascript" src="procesar.js"></script>
<!--script language='javascript' src="popcalendar.js"></script-->
<?php
/*------------------------------------------------------------------------------------------------------- */
/*                              M O D U L O  -  A B M  -  RRHH                                            */
/*------------------------------------------------------------------------------------------------------- */

	session_start();
	if(!$_SESSION["auth"]) header("Location: index.php?e=true");
	include('data.php');
	include('fechas.php');

?>
<body vlink=#999999>
<?php encabezado($_SESSION["apenom"],$_SESSION["nivel"]);

	$miconexion = conectar_mysql();

?>

 <br><br><br><br>
 <hr>
<form action="diagramas.php" method="POST" name="formbuscar" id="formbuscar">

  <table width="87%"  border="0" align="center" bgcolor="#0099ff">
  <tr><td><div align="center">
        <select name="empleado" id="empleado" tabindex="1">
          <option>(seleccione un empleado)</option>
          <?php
		$result = mysql_query("SELECT *, concat(upper(apellido), ', ', nombre) as apenom FROM empleados WHERE activo=1 ORDER BY apellido") or die(alerta(mysql_error()));
		while($data=mysql_fetch_array($result))
		{
			print "<option value=\"$data[legajo]\">$data[apenom] - $data[legajo]</option>\n";
		}
		?>

        </select>        </div></td>
        </tr>
  </table>
</form>
<img src="loading.gif" style="display:none;" alt="Cargando" id="imgciudad"/>
<div id="diagrama">
</div>

<?php

?>
<hr>
<?php
if(isset($miconexion)){
	mysql_close($miconexion) or die(alerta(mysql_error()));
}

piepagina($_SESSION["nivel"]);?>
</body>
</html>
