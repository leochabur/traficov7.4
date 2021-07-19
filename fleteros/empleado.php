
<?php
@session_start();
/*------------------------------------------------------------------------------------------------------- */
/*                              M O D U L O  -  A B M  -  RRHH                                            */
/*------------------------------------------------------------------------------------------------------- */


	if(!$_SESSION["auth"]) header("Location: index.php?e=true");
	include('data.php');
	@include('fechas.php');

?>
<body vlink=#999999>
<?php encabezado($_SESSION["chofer"],$_SESSION["nivel"]);

	$miconexion = conectar_mysql();
	$key=false;
	$inact="";
	$act="";
//	if( ( isset( $_POST['Submit2']) && ($_POST['Submit2'] == "Buscar")) || $_GET['legajo']  ) {


	//	if( $_GET['legajo'] )
	//	{
			$resul = mysql_query("SELECT ch.legajo, Concat(Upper(apellido),', ', Upper(nombre)) as chofer, ch.domicilio, ch.telefono, DATE_FORMAT(ch.fechanac, '%d/%m/%y') as fecha, concat(ch.tipodoc,' ', ch.nrodoc) as dni, ch.cuil, l.localidad, n.nacionalidad
                                  FROM empleados ch
                                       LEFT JOIN localidades l ON ch.id_localidad = l.id_localidad
                                       LEFT JOIN nacionalidades n ON ch.id_nacionalidad = n.id_nacionalidad
                                  WHERE id_empleado = ".$_SESSION['id_chofer']) or die(alerta(mysql_error()));
		//}

		if(mysql_num_rows($resul)==0) alerta("ERROR: EL EMPLEADO ES INEXISTENTE");
		else
		{
			$res=mysql_fetch_assoc($resul) or die(alerta(mysql_error()));

			$legajo=$res['legajo'];
			$nombre=$res['chofer'];
			$domicilio=$res['domicilio'];
			$telefono=$res['telefono'];
			$fecha_nac=$res['fecha'];
			$dni=$res['dni'];
			$cuil=$res['cuil'];
			$localidad=$res['localidad'];
			$nacionalidad=$res['nacionalidad'];
			
			$key=true;
		}

?>
<br>
<div align="left"><p class="tgrande"><span class="celeste"> &gt; </span><?echo $_SESSION["chofer"]?></p></div>


<?/*php if(isset($id_empleado)) print"<form action=\"abm.php?id=$id_empleado\" method=\"POST\" name=\"formagregar\" id=\"formagregar\" onSubmit=\"return valida();\">";
		else print"<form action=\"abm.php\" method=\"POST\" name=\"formagregar\" id=\"formagregar\" onSubmit=\"return valida();\">"
 */?>
<table width="67%"  border="0" align="center" bgcolor="#CCCCCC">
  --
  <tr>
    <td width="27%"><div align="right">Legajo: </div></td>
    <td ><span class="tmediano"><span class="celeste"> &gt; </span><?echo $legajo?></span></td>
  </tr>
  <tr>
    <td><div align="right">Domicilio: </div></td>
    <td>
        <span class="tmediano"><span class="celeste"> &gt; </span><?echo $domicilio?></span>
    </td>
  </tr>
  <tr>
    <td><div align="right">Localidad: </div></td>
    <td><span class="tmediano"><span class="celeste"> &gt; </span><?echo $localidad?></span></td>
  </tr>
  <tr>
    <td><div align="right">Tel&eacute;fono: </div></td>
    <td>
        <span class="tmediano"><span class="celeste"> &gt; </span><?echo $telefono?></span>
    </td>
  </tr>
  <tr>
    <td><div align="right">Nacionalidad: </div></td>

    <td><span class="tmediano"><span class="celeste"> &gt; </span><?echo $nacionalidad?></span></td>
  </tr>
  <tr>
      <td><div align="right">Fecha de nacimiento:</div> </div></td>
       <td><span class="tmediano"><span class="celeste"> &gt; </span><?echo $fecha_nac?></span></td>
  </tr>
  <tr>
    <td><div align="right">Documento: </div></td>
    <td><span class="tmediano"><span class="celeste"> &gt; </span><?echo $dni?></span></td>
    <td>&nbsp; </td>
  </tr>
  <tr>
    <td><div align="right">CUIL:</div></td>
    <td><span class="tmediano"><span class="celeste"> &gt; </span><?echo $cuil?></span></td>
    <td>&nbsp; </td>
  </tr>
  <?
    $query="SELECT upper(l.licencia) as licencia, date_format(max(lc.vigencia_hasta),'%d/%m/%Y') as hasta
                   FROM licenciaconductor lc
                   inner join licencias l on lc.id_licencia = l.id
                   WHERE lc.id_conductor = $_SESSION[id_chofer]
                   group by lc.id_licencia";
    $result=mysql_query($query) or die(alerta(mysql_error()));
   	while($data=mysql_fetch_array($result)){
         print "<tr>
                    <td><div align=\"right\">$data[licencia]: </div></td>
                    <td><span class=\"tmediano\"><span class=\"celeste\"> &gt; </span>$data[hasta]</span></td>
                    <td>&nbsp;</td>
               </tr>";
   	}
  ?>
</table>
<!--</form>!-->
<hr>
<?php
if(isset($miconexion)){
	mysql_close($miconexion) or die(alerta(mysql_error()));
}

piepagina($_SESSION["nivel"]);?>
</body>
</html>
