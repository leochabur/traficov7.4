<?php
    session_start();
	if(!$_SESSION["auth"]) header("Location:index.php?e=true");
	include('../data.php');
	include('../fechas.php');
    $cn = mysql_connect('trafico.masterbus.net', 'masterbus', 'master,07a');
    mysql_select_db('trafico', $cn);

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

function cambioEstado(id_micro, vtv, sel){
         if (sel){
            $.post("modVtv.php", { interno: id_micro, tipovtv: vtv, accion: 'add'} );
         }
         else{
            $.post("modVtv.php", { interno: id_micro, tipovtv: vtv, accion: 'del'} );
         }
}

</script>
 <p class="tgrande"><span class="celeste"> &gt; </span>Configurar VTV por Internos</p>
   <hr>
<form method="POST">
<table border="0" width="50%" rules="rows"  align="center" id="rounded-corner">
    <thead>
<tr>
    <th>Interno</th>
    <th>VTV Provincia</th>
    <th>VTV Nacion</th>
</tr>
    </thead>
        <tbody>
      <?php
        $query = "SELECT id_micro, interno, (SELECT tipo_vtv FROM tipoVtvXInterno where (id_interno = id_micro) and (tipo_vtv = 'Provincia')) as pcia, (SELECT tipo_vtv FROM tipoVtvXInterno where (id_interno = id_micro) and (tipo_vtv = 'Nacion')) as nacion
                  FROM (SELECT id_micro, interno FROM micros  where (activo)) m
                  order by interno";
        $result = mysql_query($query, $cn);
        while ($data = mysql_fetch_array($result)){
              echo "<tr>
                        <td>$data[interno]</td>";
              $sel='';
              if ($data['pcia']){
                 $sel = 'checked';
              }
              echo "<td><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id_micro], 'Provincia', this.checked);\"></td>";
              $sel='';
              if ($data['nacion']){
                 $sel = 'checked';
              }
              echo "<td><input type=\"checkbox\" $sel onClick=\"cambioEstado($data[id_micro], 'Nacion', this.checked);\"></td>
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
if(isset($conn)){
	mysql_close($conn) or die(alerta(mysql_error()));
piepagina($_SESSION["nivel"]);
}
?>
</BODY>
</HTML>
