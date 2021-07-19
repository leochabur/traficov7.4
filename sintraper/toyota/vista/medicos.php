<script language='javascript' src="popcalendar.js"></script>
<?php
        	session_start();
	if(!$_SESSION["auth"]) header("Location:index.php?e=true");
	include('data.php');
	include('fechas.php');
    $conn = conectar_mysql();
    /*SELECT *
FROM certMedicos c
inner join empleados e on e.id_empleado = c.id_empleado
inner join medicos m on m.id = c.id_medico
inner join ctrosAsistenciales ca on ca.id = c. id_ctroAsis
inner join especialidades es on es.id = c.id_especialidad
inner join diagnosticos d on d.id = c.id_diagnostico
inner join novedades n on n.id = c.id_novedad*/
    if (isset($_POST['enviar'])){
       if (isset($_POST['selNov'])){
          $medico=$_POST['selMed'];
          $espec=$_POST['selEsp'];
          $centro=$_POST['selCtro'];
          $diag=$_POST['selDiag'];
          $empleado=$_POST['selCond'];
          $novedad=$_POST['selNov'];
          $desde= $_POST['fecha_des'];
          $hasta= $_POST['fecha_has'];
          $obs= $_POST['observaciones'];
          if ($desde != ""){
             if($hasta != ""){
                       $query="INSERT INTO certMedicos (id_medico,
                                          id_ctroAsis,
                                          id_especialidad,
                                          id_diagnostico,
                                          id_empleado,
                                          id_novedad,
                                          fecha_cert,
                                          vigente_hasta,
                                          observaciones,
                                          fecha_alta_sistema,
                                          usuario_alta)
                                   VALUES ($medico,
                                          $centro,
                                          $espec,
                                          $diag,
                                          $empleado,
                                          $novedad,'".cambiaf_a_mysql($desde)."', '".cambiaf_a_mysql($hasta)."','$obs', now(), '$_SESSION[usuario]')";
                       mysql_query($query, $conn);
                       if (mysql_errno($conn)){
                          alert("Se producjeron errores al guardar el certificado ".mysql_error($conn));
                       }
                       else{
                            aviso("Certificado almacenado con exito");
                       }
             }
             else{
                  alerta("El campo 'Vigencia Hasta' no puede permanecer en blanco");
             }
         }
         else{
              alerta("El campo 'Fecha Certificado' no puede permanecer en blanco");
         }
       }
       else
           alerta("No ha seleccionado ninguna novedad!");
    }
?>

	<!--link type="text/css" href="development-bundle/themes/redmond/ui.all.css" rel="stylesheet" /-->


	<!--script type="text/javascript" src="development-bundle/jquery-1.3.2.js"></script>
	<script type="text/javascript" src="development-bundle/ui/ui.core.js"></script-->
	<!--script type="text/javascript" src="development-bundle/ui/ui.datepicker.js"></script-->
	<!--script type="text/javascript" src="development-bundle/ui/i18n/ui.datepicker-es.js"></script-->

	<!--link rel="stylesheet" href="jqtransform/jqtransformplugin/jqtransform.css" type="text/css" media="all" />
	<script type="text/javascript" src="jqtransform/jqtransformplugin/jquery.jqtransform.js" ></script-->



	
	<script type="text/javascript">
	$(function() {

        $("#selCond").change(function() {
                                       $('#novedades').load('guardar.php', {nombre:$(this).val(), accion: 'loadNov'});
        });

		$("#nuevoM").click(function(){
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});
		$("#saveM").click(function(){
                                     $('#medicos').load('guardar.php', {apellido:$('#ape').val(), nombre:$('#nom').val(), accion: 'med'});
                                     $('#ape').val('');
                                     $('#nom').val('');
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});
		$("#cancelM").click(function(){
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});
		
		$("#nuevaE").click(function(){
		                             $('#nuevaE').toggle();
		                             $('#guardarE').toggle();
		                             $('#espTxt').toggle();
		});
		$("#saveE").click(function(){
                                     $('#especialidad').load('guardar.php', {nombre:$('#espec').val(), accion: 'espec'});
                                     $('#espe').val('');
		                             $('#espTxt').toggle();
		                             $('#guardarE').toggle();
		                             $('#nuevaE').toggle();
		});
		$("#cancelE").click(function(){
		                             $('#nuevaE').toggle();
		                             $('#guardarE').toggle();
		                             $('#espTxt').toggle();
		});
		
		$("#nuevoC").click(function(){
		                             $('#nuevoC').toggle();
		                             $('#guardarC').toggle();
		                             $('#ctroTxt').toggle();
		});
		$("#saveC").click(function(){
                                     $('#centroAs').load('guardar.php', {nombre:$('#ctroAsis').val(), accion: 'ctroAsis'});
                                     $('#ctroAsis').val('');
		                             $('#ctroTxt').toggle();
		                             $('#guardarC').toggle();
		                             $('#nuevoC').toggle();
		});
		$("#cancelC").click(function(){
		                             $('#nuevoC').toggle();
		                             $('#guardarC').toggle();
		                             $('#ctroTxt').toggle();
		});
		
		$("#nuevoD").click(function(){
		                             $('#nuevoD').toggle();
		                             $('#guardarD').toggle();
		                             $('#diagTxt').toggle();
		});
		$("#saveD").click(function(){
                                     $('#diagn').load('guardar.php', {nombre:$('#diagnostic').val(), accion: 'diagnostic'});
                                     $('#diagnostic').val('');
		                             $('#diagTxt').toggle();
		                             $('#guardarD').toggle();
		                             $('#nuevoD').toggle();
		});
		$("#cancelD").click(function(){
		                             $('#nuevoD').toggle();
		                             $('#guardarD').toggle();
		                             $('#diagTxt').toggle();
		});
		
		$("#tablita tr").mouseover(function() {
                                           $(this).css({'background-color': '#92CFCF'});
        });
        $("#tablita tr").mouseout(function() {
                                           $(this).css({'background-color': '#0099ff'});
        });

	});
	
    function incrementar(){
             sFec0 = $('#fechaCert').val();
             dias = $('#diasCert').val();
             var nDia = Number(sFec0.substr(0, 2));
             var nMes = Number(sFec0.substr(3, 2));
             var nAno = Number(sFec0.substr(6, 4));

             var fechaInicial = new Date(nAno, nMes, nDia);
             valorFecha = fechaInicial.valueOf();
             valorFechaTermino = valorFecha +  ( dias * 24 * 60 * 60 * 1000 ), // 60 días después, como milisegundos ( días * horas * minutos * segundos * milisegundos )
             fechaTermino = new Date(valorFechaTermino);
             $('#hastaCert').val(fechaTermino.getDate()+'/'+fechaTermino.getMonth()+'/'+fechaTermino.getYear());
    }

	</script>




<BODY>
 <?php encabezado($_SESSION["apenom"],$_SESSION["nivel"]);?>
 <p class="tgrande"><span class="celeste"> &gt; </span>Alta de Certificado:</p>
   <hr>
<form method="POST" action="medicos.php">
<table border="0" width="75%" class="ui-widget" rules="rows" bgcolor="#0099ff" align="center" id="tablita">
<tr>
  <td>Medico</td>
  <td><div id="medicos">
      <select size="1" id="selMed" name="selMed">
      <?php
        $query = "SELECT id, upper(concat(apellido,', ',nombre)) as nombre FROM medicos ORDER BY apellido, nombre";
        $result = mysql_query($query, $conn);
        while ($data = mysql_fetch_array($result)){
              echo "<option value='$data[id]'>$data[nombre]</option>";
        }
      ?>

      </select>
      </div>
</td>
  <td><input type="button" value="Nuevo" id="nuevoM"></td>
  <td><div id="apenomM" style="display:none">Apellido<input type="text" size="20" id="ape">Nombre<input type="text" size="20" id="nom"></div></td>
  <td><div id="guardarM" style="display:none"><input type="button" value="Guardar" id="saveM"><input type="button" value="Cancelar" id="cancelM"></div></td>
</tr>
<tr>
  <td>Especialidad</td>
  <td><div id="especialidad">
      <select size="1" id="selEsp" name="selEsp">
      <?php
        $query = "SELECT id, upper(especialidad) as esp FROM especialidades ORDER BY especialidad";
        $result = mysql_query($query, $conn);
        while ($data = mysql_fetch_array($result)){
              echo "<option value='$data[id]'>$data[esp]</option>";
        }
      ?>
      </select>
      </div>
</td>
  <td><input type="button" value="Nuevo" id="nuevaE"></td>
  <td><div id="espTxt" style="display:none">Especialidad<input type="text" size="20" id="espec"></div></td>
  <td><div id="guardarE" style="display:none"><input type="button" value="Guardar" id="saveE"><input type="button" value="Cancelar" id="cancelE"></div></td>
</tr>
<tr>
  <td>Ctro. Asistencial</td>
  <td><div id="centroAs">
      <select size="1" id="selCtro" name="selCtro">
      <?php
        $query = "SELECT id, upper(nombre) as nombre FROM ctrosAsistenciales order by nombre";
        $result = mysql_query($query, $conn);
        while ($data = mysql_fetch_array($result)){
              echo "<option value='$data[id]'>$data[nombre]</option>";
        }
      ?>
      </select>
      </div>
</td>
  <td><input type="button" value="Nuevo" id="nuevoC"></td>
  <td><div id="ctroTxt" style="display:none">Ctro Asistencial<input type="text" size="20" id="ctroAsis"></div></td>
  <td><div id="guardarC" style="display:none"><input type="button" value="Guardar" id="saveC"><input type="button" value="Cancelar" id="cancelC"></div></td>
</tr>
<tr>
  <td>Diagnostico</td>
  <td><div id="diagn">
      <select size="1" id="selDiag" name="selDiag">
      <?php
        $query = "SELECT id, upper(diagnostico) as diag FROM diagnosticos ORDER BY diagnostico";
        $result = mysql_query($query, $conn);
        while ($data = mysql_fetch_array($result)){
              echo "<option value='$data[id]'>$data[diag]</option>";
        }
      ?>
      </select>
      </div>
</td>
  <td><input type="button" value="Nuevo" id="nuevoD"></td>
  <td><div id="diagTxt" style="display:none">Diagnostico<input type="text" size="20" id="diagnostic"></div></td>
  <td><div id="guardarD" style="display:none"><input type="button" value="Guardar" id="saveD"><input type="button" value="Cancelar" id="cancelD"></div></td>
</tr>
<tr>
    <td colspan="5">
        <hr color="#202020">
    </td>
</tr>
<tr>
    <td>
    Empleado
    </td>
    <td>
        <div id="conduct">
        <select size="1" name="selCond" id="selCond">
        <?php
        $query = "SELECT id_empleado, upper(concat(apellido, ', ', nombre)) as apenom FROM empleados where activo ORDER BY apellido, nombre";
        $result = mysql_query($query, $conn);
        while ($data = mysql_fetch_array($result)){
              echo "<option value='$data[id_empleado]'>$data[apenom]</option>";
        }
      ?>
      </select>
      </div>
    </td>
    <td>
    </td>
    <td>
    </td>
    <td>
    </td>
</tr>
<tr>
    <td>
        Novedades
    </td>
    <td>
        <div id="novedades">
        </div>
    </td>
</tr>
<tr>
<td>
    Fecha Certificado
</td>
  <td colspan="5"><div><input name="fecha_des" type="text" id="fecha_des" onClick="popUpCalendar(this, fecha_des, 'dd-mm-yyyy');" size="10"></div></td>
</tr>
<tr>
<td>
    Vigencia Hasta
</td>
  <td colspan="5"><div><input name="fecha_has" type="text" id="fecha_has" onClick="popUpCalendar(this, fecha_has, 'dd-mm-yyyy');" size="10"></div></td>
</tr>
<tr>
<tr>
<td>
    Observaciones
</td>
  <td colspan="5"><div><textarea rows="4" cols="20" name="observaciones"></textarea></div></td>
</tr>
<tr>
<td colspan="6">
    <div align="right"><input type="submit" value="Guardar" name="enviar"></div>
</td>
</tr>
</table>
</form>
<hr>
<?php
if(isset($conn)){
	mysql_close($conn) or die(alerta(mysql_error()));
}

piepagina($_SESSION["nivel"]);?>
</BODY>
</HTML>
