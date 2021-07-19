<?php
    session_start();
	if(!$_SESSION["auth"]) header("Location:../index.php?e=true");
	include('../data.php');
	include('../fechas.php');
    $cn = conectar_mysql();//mysql_connect('trafico.masterbus.net', 'masterbus', 'master,07a');
  // mysql_select_db('trafico', $cn);

?>
<style type="text/css">
<!--
@import url("style.css");
-->
</style>
<BODY>
 <?php encabezado($_SESSION["apenom"],$_SESSION["nivel"]);?>

<script type="text/javascript" src="updcert/jquery.jeditable.js"></script>
<script type="text/javascript">

function cambioEstado(empleado, lic, sel){
         if (sel){
            $.post("modlic.php", { conductor: empleado, licencia: lic, accion: 'add'} );
         }
         else{
            $.post("modlic.php", { conductor: empleado, licencia: lic, accion: 'del'} );
         }
}

</script>
 <p class="tgrande"><span class="celeste"> &gt; </span>Configurar Licencias Por Conductor</p>
   <hr>
<form method="POST">
<table border="0" width="100%" rules="rows"  align="center" id="rounded-corner">
    <thead>
<tr>
    <th>Legajo</th>
    <th>Apellido, Nombre</th>
    <th>Municipal</th>
    <th>Provincial</th>
    <th>Nacional</th>
</tr>
    </thead>
        <tbody>
      <?php
        $query = "select legajo, id_empleado, upper(concat(apellido, ', ',nombre)) as nombre,
                 (select id_licencia FROM licenciasxconductor where (id_conductor = id_empleado) and (id_licencia = 1)) as nacional,
                 (select id_licencia FROM licenciasxconductor where (id_conductor = id_empleado) and (id_licencia = 2)) as provincial,
                 (select id_licencia FROM licenciasxconductor where (id_conductor = id_empleado) and (id_licencia = 3)) as municipal
                 from empleados e
                 where (activo) and (id_cargo = 1)
                 ORDER BY legajo";
        $result = mysql_query($query, $cn);
        while ($data = mysql_fetch_array($result)){
              echo "<tr>
                        <td>$data[legajo]</td>
                        <td>$data[nombre]</td>";
              $sel='';
              if ($data['municipal']){
                 $sel = 'checked';
              }
              echo "<td><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id_empleado], '3', this.checked);\"></td>";
              $sel='';
              if ($data['provincial']){
                 $sel = 'checked';
              }
              echo "<td><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id_empleado], '2', this.checked);\"></td>";
              $sel='';
              if ($data['nacional']){
                 $sel = 'checked';
              }
              echo "<td><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id_empleado], '1', this.checked);\"></td>
                   </tr>";
        }
      ?>
          </tbody>
</table>
</form>
<?php

?>

<hr>
<?php
if(isset($cn)){
	mysql_close($conn) or die(alerta(mysql_error()));
piepagina($_SESSION["nivel"]);
}
?>
</BODY>
</HTML>
