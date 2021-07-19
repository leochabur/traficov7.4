<?php
/*------------------------------------------------------------------------------------------------------ */
/*                              M O D U L O  -  A B M  -  O R D E N E S                                  */
/*------------------------------------------------------------------------------------------------------ */
/* Autor : Juan Girini <juangirini@yahoo.com.ar>           ordenes                                              */

session_start();
if(!$_SESSION["auth"] or $_SESSION["nivel"]<2) header("Location: ../index.php?e=true");
include('../data.php');
include('fechas.php');

/*
SELECT s.id_servicio, c.nombre, c.nombrecliente, s.hcitacion, s.hsalida
FROM (SELECT * FROM ordenes WHERE fservicio = '2010-01-04') o
inner join servicios s on s.id_servicio = o.id_servicio
inner join cronogramas c on c.codigo = s.cod_cronograma*/


if ((isset($_POST['desde'])) && (isset($_POST['hasta']))){
   $fecha_diag_original = $_POST['desde'];
   $fecha_diag_aaplicar = $_POST['hasta'];
   $hoy=getDate();
   $hoy=$hoy['mday'].'-'.$hoy['mon'].'-'.$hoy['year'];
   $dias=dias_entre_fechas($hoy, $fecha_diag_aaplicar);
   if ($dias < 0){//significa que se esta intentando modificar un diagrama anterior a la fecha actual
      alerta('No se pueden modificar ordenes anteriores a la fecha actual');
   }
   else{
        $dias=dias_entre_fechas($fecha_diag_original, $fecha_diag_aaplicar);
        if ($dias == 0){
           alerta('Las fechas de los diagramas son iguales. Imposible modificar las ordenes');
        }
        elseif($dias < 0){
           alerta('La fecha de las ordenes que se intenta copiar deben ser anteriores a las cuales se va a aplicar el diagrama');
        }else{
              if(isset($_POST['submit'])&&($_POST['submit']=="Copiar")){
                     $miconexion = conectar_mysql() or die(alerta(mysql_error()));//CONECTAR
                     $query="SELECT * FROM ordenes WHERE (id_orden in ($_POST[num_ordenes]))";

                     $result=mysql_query($query) or die(alerta(mysql_error()));
                     mysql_close($miconexion);
                     $i=0;
                     $miconexion = conectar_mysql() or die(alerta(mysql_error()));
                     while($data=mysql_fetch_array($result)){
                           $id_servicio= $data['id_servicio'];
                           $id_micro= $data['id_micro'];
                           $id_chofer1= $data['id_chofer1'];
                           $id_chofer2= $data['id_chofer2'];
                           $consulta = "INSERT INTO ordenes (id_servicio, fcreacion, fservicio, ffin, id_micro, kmreales, id_chofer1, id_chofer2, kmsalida, kmregreso, kminicial, kmfinal, hfinservreal, pasajeros, nrolistapas, excursion, lugar, video, jugo, cafe, pelicula, peajesac, viaticosac, alojamientoac, estac, valorviaje, observaciones, finalizada, responsable, chequeada)".
                            " values($data[id_servicio],'date(now())','".cambiaf_a_mysql($fecha_diag_aaplicar)."','".cambiaf_a_mysql($fecha_diag_aaplicar)."',$data[id_micro],'$data[kmreales]','$data[id_chofer1]','$data[id_chofer2]','$data[kmsalida]','$data[kmregreso]','$data[kminicial]','$data[kmfinal]','','','$data[nrolistapas]','$data[excursion]','$data[lugar]','$data[video]','$data[jugo]','$data[cafe]','$data[pelicula]','$data[peajesac]','$data[viaticosac]','$data[alojamientoac]','$data[estac]','$data[valor]','','0','$_SESSION[apenom]',0)";
                           mysql_query($consulta) or die(alerta(mysql_error()."6"));
                           //$actualizar_serv = "UPDATE ordenes SET id_micro = $id_micro, id_chofer1 = $id_chofer1, id_chofer2 = $id_chofer2 WHERE (id_servicio = $id_servicio) and (fservicio = '".cambiaf_a_mysql($fecha_diag_aaplicar)."')";
                           //$res = mysql_query($actualizar_serv) or die(alerta(mysql_error()));
                           $i = $i + mysql_affected_rows();
                     }
                     mysql_close();
                     aviso('Se actualizaron '.$i.' ordenes con exito');
              }
        }
   }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<?php titulo($_SESSION["apenom"]);?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="../estilo.css" rel="stylesheet" type="text/css"> 
<link href="../estilo.css" rel="stylesheet" type="text/css">

<script src="../CalendarPopup.js" type="text/javascript"></script>

<script language='javascript' src="popcalendar.js"></script>

<script src="../DynamicOptionList.js" type="text/javascript"></script>
<script src="./jquery.ui.datepicker-es.js"></script>

<style type="text/css">
<!--
.Estilo2 {
	font-size: 18px;
	font-weight: bold;
	font-family: Verdana, Arial, Helvetica, sans-serif;
}
-->
</style>
</head>

<body>

<?php
encabezado($_SESSION["apenom"],$_SESSION["nivel"]);
?>
 <script language="JavaScript">
 function valida() {

             var desde = jq13('#desde').val();
             var hasta = jq13('#hasta').val();

             var fechaActual = new Date();
             var fdesde = new Date(desde.substr(6,4),desde.substr(3,2),desde.substr(0,2),fechaActual.getHours(),fechaActual.getMinutes() ,fechaActual.getSeconds());
             var fhasta = new Date(hasta.substr(6,4),hasta.substr(3,2),hasta.substr(0,2),fechaActual.getHours(),fechaActual.getMinutes() ,fechaActual.getSeconds());

             if (fhasta.getTime() < fechaActual.getTime()){
                alert('No se pueden modificar ordenes anteriores a la fecha actual');
                return false;
             }
             else{
                  if (fhasta.getTime() == fechaActual.getTime()){
                     alert('Las fechas de los diagramas son iguales. Imposible modificar las ordenes');
                     return false;
                  }
                  else{
                       if (fdesde.getTime() > fhasta.getTime()){
                          alert('La fecha de las ordenes que se intenta copiar deben ser anteriores a las cuales se va a aplicar el diagrama');
                          return false;
                       }
                  }
             }
			return true;
 }
 
     if (!Array.indexOf) {
        Array.prototype.indexOf = function (obj, start) {
                                for (var i = (start || 0); i < this.length; i++) {
                                    if (this[i] == obj) {
                                       return i;
                                    }
                                }
                                return -1;
                                }
        }
 	jq13(document).ready(function(){
       jq13('#form1 input:text').datepicker({showOn: 'button',
                                            buttonText: "Seleccionar Fecha",
                                            dateFormat: 'dd-mm-yy',
                                            		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
		monthStatus: 'Seleccionar otro mes',
		yearStatus: 'Seleccionar otro año',
		weekHeader: 'Sm',
		weekStatus: 'Semana del año',
		dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
		dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
		dayStatus: 'Set DD as first week day',
		dateStatus: 'Select D, M d',
		dateFormat: 'dd-mm-yy'});
       jq13('#cargar').click(function(){
                                          jq13('#diagrama').html('<img src="bigrotation2.gif" width="32" height="32" border="0">');
                                          jq13('#diagrama').load('cargarServicios.php', {fecha: jq13('#desde').val()});
                                       });

       jq13("#form1").submit(function() {
                                         if (valida()){
                                            if (confirm('Se copiaran '+ordenes.length+' ordenes. Seguro aplicar los cambios?')){
                                               jq13('#num_ordenes').val(ordenes.join());
                                               return true;
                                            }
                                             return false;
                                         }
                                         return false;
                                        });


    });

    function cargarCheck(orden){
             if (orden.checked){
                ordenes.push(orden.id);
             }
             else{
                  var a = ordenes.indexOf(orden.id);
                  ordenes.splice(a,1);
             }
    }
    
    function guardarOrdenes(){
             jq13('#num_ordenes').val(ordenes.join());
    }
    
    function checkTodos (obj) {
             jq13("#tablita input:checkbox").attr('checked', obj.checked);
    }


</script>
<p><span class="celeste Estilo2">&gt; </span><span class="tgrande">Copiar diagrama del Dia...</span></p>
<form name="form1" id="form1" action="./copiardiagrama.php" method="POST">

<table width="67%" border="0" align="center" bgcolor="#0099ff" name="tableta">
<tr>
        <td >
        <div align="center">
             <input type="text" name="desde" id="desde"/></td>
        </div>
        </td>
        <td>
            <input type="button" value="Cargar Diagrama" id="cargar">
        </td>
</tr>
</table>
<br>
<!--table width="67%" border="0" align="center" bgcolor="#0099ff">
<tr>
<td align="left"><input type="checkbox" onClick="checkTodos(this);">Marcar Todos</td>
</tr>
</table!-->
<div id="diagrama">

</div>

<hr>
<div align="left"><span class="celeste Estilo2">&gt; </span><span
	class="tgrande">Aplicar a... </span></div>
<table width="67%" border="0" align="center" bgcolor="#0099ff">
<tr>
        <td >
        <div align="center">
             <input type="text" name="hasta" id="hasta"/></td>
        </div>
        </td>
</tr>


</table>

<hr>
<table width="67%" border="0" align="center" bgcolor="#0099ff">
<tr>
        <td >
        <div align="center">
             <input type="submit" name="submit" value="Copiar Diagrama" id="copiar">
        </div>
        </td>
</tr>


</table>
<hr>
<input type="hidden" size="20" id="num_ordenes" name="num_ordenes">
</form>

</p>
<p>&nbsp;</p>
<p class="tmediano"><span class="tmediano"><span class="celeste">&gt; </span><a
	href="./index.php">Volver al menú anterior</a></span> <?php
	if(isset($miconexion)){
		mysql_close($miconexion) or die(alerta(mysql_error()));
	}

	piepagina($_SESSION["nivel"]); ?></p>
</body>
</html>
