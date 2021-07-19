<?php
    session_start();
	if(!$_SESSION["auth"]) header("Location:index.php?e=true");
	include('data.php');
	include('fechas.php');
    $cn = conectar_mysql();

?>

<BODY>
 <?php encabezado($_SESSION["apenom"],$_SESSION["nivel"]);?>

<script type="text/javascript" src="updcert/jquery.jeditable.js"></script>



 	<script type="text/javascript">
	$(document).ready(function(){

    });

	</script>
 <p class="tgrande"><span class="celeste"> &gt; </span>Modificacion de Certificados:</p>
   <hr>
<form method="POST">
<table border="0" width="75%" class="ui-widget" rules="rows" bgcolor="#0099ff" align="center" id="tablita">
<tr>
  <td>Empleados</td>
  <td><div id="medicos">
      <select size="1" id="selEmp" name="selEmp">
              <option value="0">SELECCIONE UN EMPLEADO</option>
              <option value="0"></option>
      <?php
        $query = "SELECT id_empleado as id, upper(concat(apellido,', ',nombre)) as empleado FROM empleados where activo ORDER BY apellido, nombre";
        $result = mysql_query($query, $cn);
        while ($data = mysql_fetch_array($result)){
              echo "<option value='$data[id]'>$data[empleado]</option>";
        }
      ?>
      </select>
      </div>
  </td>
  <td>
      <input type="submit" value="Buscar">
  </td>
</tr>
</table>
</form>
<?php
if (isset($_POST['selEmp'])){
   $query = mysql_query("SELECT c.id,
                             DATE_FORMAT(c.fecha_cert, '%d/%m/%Y') as fecCert,
                             DATE_FORMAT(c.vigente_hasta, '%d/%m/%Y') as hastaCert,
                             concat(upper(m.apellido),', ', upper(m.nombre)) as medico,
                             upper(ca.nombre) as centro,
                             upper(es.especialidad) as espe,
                             upper(d.diagnostico) as diag,
                             DATE_FORMAT(n.desde, '%d/%m/%Y') as fecNov
                      FROM certMedicos c
                      inner join medicos m on m.id = c.id_medico
                      inner join ctrosAsistenciales ca on ca.id = c. id_ctroAsis
                      inner join especialidades es on es.id = c.id_especialidad
                      inner join diagnosticos d on d.id = c.id_diagnostico
                      inner join novedades n on n.id = c.id_novedad
                      where (c.id_empleado = $_POST[selEmp])", $cn);



$qdiag = "SELECT id, upper(diagnostico) as diag
             FROM diagnosticos
             order by diagnostico";
$result = mysql_query($qdiag, $cn);
while ($data = mysql_fetch_array($result)){
      $diag["$data[id]"] =  "$data[diag]";
 }
asort($diag);
mysql_free_result($result);
$qmedico = "SELECT id, upper(concat(apellido,', ', nombre)) as medico
          FROM medicos
          ORDER BY apellido, nombre";
$result = mysql_query($qmedico, $cn);
while ($data = mysql_fetch_array($result)){
      $medico["$data[id]"] =  "$data[medico]";
 }
asort($medico);
mysql_free_result($result);
$qctro = "SELECT id, upper(nombre) as nombre
          FROM ctrosAsistenciales
          order by nombre";
$result = mysql_query($qctro, $cn);
while ($data = mysql_fetch_array($result)){
      $ctro["$data[id]"] =  "$data[nombre]";
}
asort($ctro);
mysql_free_result($result);
$empleado = "SELECT concat(upper(apellido), ', ',UPPER(nombre),'  -  LEGAJO: ', legajo) as nombre
             FROM empleados
             where id_empleado = $_POST[selEmp]";
$result = mysql_query($empleado, $cn);
if ($data = mysql_fetch_array($result)){
   $empleado = $data['nombre'];
}
mysql_free_result($result);
$script="<script type=\"text/javascript\">
$(document).ready(function() {
     $(\"#selEmp option[value=$_POST[selEmp]]\").attr(\"selected\",true);

	 $('.diag').editable('save.php', {
         data   : '".json_encode($diag)."',
		 type   : 'select',
		 submit : 'Guardar'
	 });
	 $('.medico').editable('save.php', {
         data   : '".json_encode($medico)."',
		 type   : 'select',
		 submit : 'Guardar'
	 });
	 $('.centro').editable('save.php', {
         data   : '".json_encode($ctro)."',
		 type   : 'select',
		 submit : 'Guardar'
	 });
	 $(\"#table\").tablesorter();
 });
 </script>
	<fieldset id=\"content\">
    	<legend>Certificados Correspondientes a <b><u>$empleado</u></b></legend>
    	<table id='table' width=\"75%\" align=\"center\" class=\"tablesorter\">
            <thead>
            	<tr class='head'>
                    <th>Fecha Certificado</th>
                    <th>Vigente Hasta</th>
                    <th>Medico</th>
                    <th>Centro Asistencial</th>
                    <th>Diagnostico</th>
                </tr>
            </thead>
            <tbody>";

				while($row = mysql_fetch_array($query))
				{
					$id = $row['id'];

					$script.="<tr>
                        <td><div class=\"text\" id=\"fecha_cert-$id\">$row[fecCert]</div></td>
                        <td><div class=\"text\" id=\"vigente_hasta-$id\">$row[hastaCert]</div></td>
                        <td><div class=\"medico\" id=\"id_medico-$id\">$row[medico]</div></td>
                        <td><div class=\"centro\" id=\"id_ctroAsis-$id\">$row[centro]</div></td>
                        <td><div class=\"diag\" id=\"id_diagnostico-$id\">$row[diag]</div></td>
                    </tr>";

				}
    $script.="</tbody>
                      </table>
                      </fieldset>";
mysql_free_result($query);
echo $script;
     
}
?>

<hr>
<?php
if(isset($conn)){
	mysql_close($conn) or die(alerta(mysql_error()));
piepagina($_SESSION["nivel"]);
}
?>
</BODY>
</HTML>
