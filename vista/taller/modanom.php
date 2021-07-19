<?php
     session_start();
     include_once('../paneles/viewpanel.php');
$tabla='<link type="text/css" href="/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="/vista/css/estilos.css" rel="stylesheet" type="text/css" />
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
                                                                     $.post("/modelo/taller/reanom.php", 
                                                                            datos, 
                                                                            function(data){
                                                                                            console.log(data);
                                                                                            $("#dialog").dialog("close");
                                                                                          });
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
                                 $con = conexcion(true);
                                 $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha,
                                                date_format(fecha_reparacion, '%d/%m/%Y') as fecha_reparacion,
                                                interno,
                                                rubro,
                                                detalle_anomalia,
                                                date_format(a.fecha_alta, '%d/%m/%Y - %H:%i') as generada,
                                                if (a.id_empleado is not null, upper(concat(e.apellido,', ', e.nombre)), upper(apenom)) as creada_por,
                                                observacion_taller,
                                                a.id,
                                                if(reparada, 'checked', '') as reparada,
                                                reparada as repa,
                                                time_format(hora_reparacion, '%H:%i') as hora_reparacion,
                                                orden_trabajo,
                                                id_unidad,
                                                id_rubroanomalia,
                                                a.fecha_alta
                                         FROM anomalias a
                                         inner join rubros_anomalias r on r.id = a.id_rubroanomalia
                                         inner join (select * from unidades) u on u.id = a.id_unidad
                                         left join (select * from empleados ) e on e.id_empleado = a.id_empleado
                                         left join usuarios us on us.id = a.id_usuario_alta
                                         where a.id = $_POST[orden]";
                               //  die($sql);
                                 $query = mysqli_query($con, $sql) or die(mysqli_error($con));
				                 if($row = mysqli_fetch_array($query))
                                 {
                                            $sqlBefore = "SELECT id, detalle_anomalia, date_format(fecha, '%d/%m/%Y') as generada
                                                          FROM anomalias
                                                          WHERE activa and 
                                                                id_unidad = $row[id_unidad] and 
                                                                id_rubroanomalia = $row[id_rubroanomalia] and 
                                                                fecha_alta <= '$row[fecha_alta]' and 
                                                                id <> $_POST[orden] and
                                                                not reparada
                                                           ORDER BY fecha DESC";

                                            $before = mysqli_query($con, $sqlBefore);

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
                                 $asoc = '';
                                 if (mysqli_num_rows($before))
                                 {

                                    $asoc = "<hr>
                                             <table class='table table-zebra'>
                                                <thead>
                                                    <tr>
                                                        <th>Asociar</th>
                                                        <th>Fecha</th>
                                                        <th>Anomalia</th>
                                                    </tr>
                                                </thead>
                                                <tbody>";

                                     while ($rowAsoc = mysqli_fetch_array($before))
                                     {
                                            $asoc.="<tr>
                                                        <td>
                                                            <input type='checkbox' name='ASOC[]' value='$rowAsoc[id]'/>
                                                        </td>
                                                        <td>
                                                            $rowAsoc[generada]
                                                        </td>
                                                        <td>
                                                            $rowAsoc[detalle_anomalia]
                                                        </td>
                                                    </tr>";
                                     }
                                     $asoc.='</tbody>
                                             </table>
                                             <hr>';
                                 }
                                 $tabla.='</table>
                                          '.$asoc.'
	                                      </fieldset>
	                                      <input type="hidden" name="accion" value="modanom">
	                                      <input type="hidden" name="anomalia" value="'.$row['id'].'">
	                                      </form>
                                          </BODY>
                                          </HTML>';


                                 if ($row['repa']){
                                    $tabla.='<script>$(".state").removeAttr("disabled");</script>';
                                 }
                                 @mysql_free_result($query);
                                 @mysql_close($con);
print $tabla;
?>

