<?php
     session_start();
     include_once('../paneles/viewpanel.php');
$tabla='<link type="text/css" href="/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="/vista/js/validate-form/jquery.validate.min.js"></script>
  
  
<script type="text/javascript" src="/vista/js/validate-form/jquery.metadata.js"></script>
<script>
 
	$(function() {

                 $.mask.definitions["~"]="[012]";
                 $.mask.definitions["%"]="[012345]";
                 $(".hora").mask("~9:%9",{completed:function(){}});
                 $("#frepa").datepicker({dateFormat:"dd/mm/yy"});
                 $("#change").button();
                 $("#close").button().click(function(){
                                                       $("#dialog").dialog("close");
                                                       });
                 $("#modorden").validate({
                                           submitHandler: function(){
                                                                     var datos = $("#modorden").serialize();
                                                                     $.post("/modelo/taller/reanom.php", datos, function(data){$("#dialog").dialog("close");});
                                                                     }
                                         });
                 $(".state").attr("disabled", "disabled");

	});
	
	function desactivar() {
            if($("#rpda").prop("checked")) {
                                              $(".state").removeAttr("disabled");
            }
            else{
                                              $(".state").attr("disabled", "disabled");
            }
    }
	
</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#modorden .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<BODY>';
    $tabla.="<br><br>
             <div id='result'></div>

             <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                       <legend class='ui-widget ui-widget-header ui-corner-all'>Descripcion de anomalia</legend>
                       <table id='table'>";
                                 $con = conexcion();
                                 $sql = "SELECT legajo,  upper(concat(apellido,', ', nombre)) as empleado, nrodoc, siniestro_numero, date_format(fecha_siniestro, '%d/%m/%Y') as fecha, time_format(hora_siniestro, '%H:%i') as hora,
       ec.estado, cu.codigo, calle1, calle2, upper(ci.ciudad) as ciudad, upper(tl.tipo) as tipolesion, if(resp_estimada is null, '', if(resp_estimada = 'c', 'CON', 'SIN')) as resp,
       upper(ca.cobertura) as cobertura, interno, coa.compania, numero_poliza, indemnizacion_a_terceros, s.id
FROM siniestros s
left join empleados e on e.id_empleado = s.id_empleado
left join estadoClima ec on ec.id = s.estado_clima
left join codUbicacionSiniestro cu on cu.id = s.cod_ubicacion
left join ciudades ci on ci.id = s.id_localidad
left join tipoLesionSiniestro tl on tl.id = s.tipo_lesion
left join coberturaAfectadaSiniestro ca on ca.id = s.cobertura_afectada
left join unidades u on u.id = s.id_coche
left join clientes cl on cl.id = s.id_cliente
left join companiasAseguradoras coa on coa.id = s.compania_seguro
where s.id =  $_POST[orden]";
                               //  die($sql);
                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
				                 if($row = mysql_fetch_array($query)){
					                        $tabla.="<tr>
                                                         <td>N".htmlentities("°")." de Anomalia</td>
                                                         <td><input type='text' size='20' value='$_POST[orden]' readonly class='ui-widget ui-widget-content  ui-corner-all'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Fecha</td>
                                                         <td><input id='fservicio' name='fservicio' type='text' size='20' value='$row[fecha]' class='ui-widget ui-widget-content  ui-corner-all {required:true}'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Generada por</td>
                                                         <td><input type='text' size='30' value='$row[creada_por]' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Interno</td>
                                                         <td><input id='nombre' name='nombre' type='text' size='30' class='ui-widget ui-widget-content  ui-corner-all {required:true}' value='$row[interno]'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Rubro anomalia</td>
                                                         <td><input id='hcitacion' name='hcitacion' class='ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='30' value='$row[rubro]'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Detalle anomalia</td>
                                                         <td><textarea rows='3' cols='35' class='ui-widget ui-widget-content  ui-corner-all {required:true}' readonly>$row[detalle_anomalia]</textarea></td>
                                                     </tr>
                                                         <td>Generada el...</td>
                                                         <td><input id='hfinserv' name='hfinserv' class='ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='20' value='$row[generada]'></td>
                                                     </tr>
                                                     </table>
	                                      </fieldset>
	                                      <form id='modorden'>
                                          <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                       <legend class='ui-widget ui-widget-header ui-corner-all'>Acciones de taller</legend>
                                       <table>
                                                     <tr>
                                                         <td>Reparada</td>
                                                         <td><input type='checkbox' id='rpda' name='rpda' value='1' onclick='desactivar()' $row[reparada]></td>
                                                     </tr>
                                                     <tr class='state'>
                                                         <td>Fecha Reparacion</td>
                                                         <td><input id='frepa' name='frepa' type='text' size='20' value='$row[fecha_reparacion]' class='ui-widget ui-widget-content  ui-corner-all state'></td>
                                                     </tr>
                                                     <tr class='state'>
                                                         <td>Hora Reparacion</td>
                                                         <td><input id='hrepa' name='hrepa' type='text' size='5' value='$row[hora_reparacion]' class='hora ui-widget ui-widget-content  ui-corner-all state'></td>
                                                     </tr>
                                                     <tr class='state'>
                                                         <td>Orden Trabajo</td>
                                                         <td><input id='orepa' name='orepa' type='text' size='10' value='$row[orden_trabajo]' class='ui-widget ui-widget-content  ui-corner-all state'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Observaciones Taller</td>
                                                         <td><textarea rows='3' name='otaller' cols='35' class='ui-widget ui-widget-content  ui-corner-all'>$row[observacion_taller]</textarea></td>
                                                     </tr>

                                                     <tr>
                                                         <td colspan='2'><input type='button' id='close' value='Cerrar ventana'><input type='submit' id='change' value='Guardar Cambios'></td>
                                                     </tr>";
                                 }
                                 $tabla.='</table>
	                                      </fieldset>
	                                      <input type="hidden" name="accion" value="modanom">
	                                      <input type="hidden" name="anomalia" value="'.$row[id].'">
	                                      </form>
                                          </BODY>
                                          </HTML>';
                                 if ($row[repa]){
                                    $tabla.='<script>$(".state").removeAttr("disabled");</script>';
                                 }
                                 @mysql_free_result($query);
                                 @mysql_close($con);
print $tabla;
?>

