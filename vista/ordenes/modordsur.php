<?php
     session_start();
     include_once('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');
     $res_cc = ejecutarSQL("SELECT cant_cond FROM estructuras WHERE id = $_SESSION[structure]");
     if ($data_cc = mysql_fetch_array($res_cc)){
         $cantTripulacion = $data_cc[0];
     }

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
                 $(".hora").mask("99/99/9999 ~9:%9",{completed:function(){}});
                 $("#fservicio").datepicker({dateFormat:"dd/mm/yy"});
                 $("#change").button();
                 $("#close").button().click(function(){
                                                       $("#dialog").dialog("close");
                                                       });
                 $("#modorden").validate({
                                           submitHandler: function(){
                                                                     var asoc = $("#tieneasoc").val();
                                                                     if (asoc == 1){
                                                                        if (confirm("La orden que intenta modificar, tiene asociadas un conjunto de ordenes!. Modificar todas?")){
                                                                           asoc = 1;
                                                                        }
                                                                        else{
                                                                             asoc = 0;
                                                                        }
                                                                     }
                                                                     var datos = $("#modorden").serialize();
                                                                     datos = datos+"&asocia="+asoc;

                                                                     $.post("/modelo/ordenes/modordsur.php", datos, function(data){

                                                                                                                                if (data == 1){
                                                                                                                                   $("#dialog").dialog("close");
                                                                                                                                   $("#data").html("<div align=\'center\'><img  alt=\'cargando\' src=\'../../ajax-loader.gif\' /></div>");
                                                                                                                                   var datos = $("#upuda").serialize();
                                                                                                                                   $.post("/modelo/informes/trafico/diagdiasur.php", datos, function(data){
                                                                                                                                                                                                        $("#data").html(data);
                                                                                                                                                                                                        $("body").animate({
                                                                                                                                                                                                                           scrollTop: ($("#posy").val()-100)+"px"
                                                                                                                                                                                                                           },
                                                                                                                                                                                                                           0);

                                                                                   });
                                                                                                                                }
                                                                                                                                else{
                                                                                                                                    alert(data);
                                                                                                                                     alert("Error al modificar la orden!!!");

                                                                                                                                }
                                                                                                                                }).fail(function(data) { console.log(data); alert("Error al modificar la orden!");});
                                                                     }
                                         });
                 $.post("/vista/ordenes/cargar_combo_conductores.php", {orden: '.$_POST[orden].'}, function(data){
                                                                                                  $("#chofer1").append(data);
                                                                                                  $("#chofer2").append(data);';
                                                                                                  for ($i = 3; $i <= $cantTripulacion; $i++){
                                                                                                    $tabla.="$('#chofer$i').append(data);";
                                                                                                  }

        $tabla.='});
                 $.post("/vista/ordenes/cargar_combo_internos.php", {orden: '.$_POST[orden].'}, function(data){
                                                                                                  $("#interno").append(data);

                                                                                           });

	});
	
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
             <form id='modorden'>
             <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                       <legend class='ui-widget ui-widget-header ui-corner-all'>Orden de Trabajo N".htmlentities("�")." $_POST[orden]</legend>
                       <div id='mjw'></div>
                       <div id='tablaordenes'>
                       <table id='table'>";
                                 $con = conexcion();
                                 $sql = "SELECT o.id, o.km, date_format(date(citacion),'%d/%m/%Y') as fservicio, finalizada, date_format(citacion, '%d/%m/%Y %H:%i') as hcitacion, 
                                                date_format(salida, '%d/%m/%Y %H:%i') as hsalida, date_format(finalizacion, '%d/%m/%Y %H:%i') as hfinserv, date_format(llegada, '%d/%m/%Y %H:%i') as hllegada,
                                                o.nombre, if(em1.id = 1,concat(ch1.apellido, ', ',ch1.nombre),
                                                concat('(',em1.razon_social,') ', ch1.apellido, ', ',ch1.nombre)) as chofer1, upper(c.razon_social) as razon_social,
                                                concat(ch2.apellido, ', ',ch2.nombre) as chofer2, comentario, interno, m.id as id_micro, ch1.id_empleado as id_chofer1, ch2.id_empleado as id_chofer2,
                                                ori.ciudad as origen, des.ciudad as destino, if (o.borrada, 'selected','') as borrada, if (o.finalizada, 'selected','') as finalizada, vacio, cv.id as id_cli_vac, upper(cv.razon_social) as rsclivac, if(oa.id is null, 0, 1) as tiene_asoc
                                                FROM ordenes o
                                                INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura                                                
                                                LEFT JOIN empleados ch1 ON (ch1.id_empleado = o.id_chofer_1)
                                                LEFT JOIN empleadores em1 ON (em1.id = ch1.id_empleador)
                                                inner join ciudades ori on (ori.id = id_ciudad_origen) and (ori.id_estructura = id_estructura_ciudad_origen)
                                                inner join ciudades des on (des.id = id_ciudad_destino) and (des.id_estructura = id_estructura_ciudad_destino)
                                                LEFT JOIN empleados ch2 ON (ch2.id_empleado = o.id_chofer_2)
                                                LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                                LEFT JOIN clientes cv ON ((cv.id = o.id_cliente_vacio) and (cv.id_estructura = o.id_estructura_cliente_vacio))
                                                LEFT JOIN unidades m ON (m.id = o.id_micro)
                                                left join ordenes_asocioadas oa on oa.id_orden = o.id and oa.id_estructura_orden = o.id_estructura
                                                WHERE o.id = $_POST[orden]";

                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
				                 if($row = mysql_fetch_array($query)){
                                            $comentario = $row['comentario'];
					                        $tabla.="<tr>
                                                         <td>N".htmlentities("�")." de Orden</td>
                                                         <td><input type='text' size='20' value='$_POST[orden]' readonly class='ui-widget ui-widget-content  ui-corner-all'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Fecha de Servicio</td>
                                                         <td><input id='fservicio' name='fservicio' type='text' size='20' value='$row[fservicio]' class='ui-widget ui-widget-content  ui-corner-all {required:true}'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Cliente</td>
                                                         <td><input type='text' size='30' value='".htmlentities($row[razon_social])."' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>";
                                            if ($row['vacio']){
                                               $clivac="";
                                               if ($row['id_cli_vac']){
                                                  $clivac="<option value='$row[id_cli_vac]'>$row[rsclivac]</option>";
                                               }
                                               $tabla.="<tr>
                                                            <td>Afectar Vacio a...</td>
                                                            <td><select id='clivac' name='clivac'>
                                                                        $clivac
                                                                        <option value'0'></option>
                                                                        ".armarSelect('clientes', 'razon_social', 'id', 'razon_social', "id_estructura = $_SESSION[structure]", 1)."
                                                                </select>
                                                            </td>
                                                        </tr>";
                                            }
                                            /*                                                     <tr>
                                                         <td>Hora Llegada</td>
                                                         <td><input id='hllegada' name='hllegada' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='5' value='$row[hllegada]'></td>
                                                     </tr>*/
                                            $tabla.="<tr>
                                                         <td>Nombre Servicio</td>
                                                         <td><input id='nombre' name='nombre' type='text' size='30' class='ui-widget ui-widget-content  ui-corner-all {required:true}' value='".htmlentities($row[nombre])."'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Hora Citacion</td>
                                                         <td><input id='hcitacion' name='hcitacion' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hcitacion]'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Hora Salida</td>
                                                         <td><input id='hsalida' name='hsalida' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hsalida]'></td>
                                                     </tr>
                                                     <td>Hora Llegada</td>
                                                         <td><input id='hllegada' name='hllegada' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hllegada]'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Hora Fin Servicio</td>
                                                         <td><input id='hfinserv' name='hfinserv' class='hora ui-widget ui-widget-content  ui-corner-all {required:true}' type='text' size='15' value='$row[hfinserv]'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Origen</td>
                                                         <td><input type='text' size='20' value='".htmlentities($row[origen])."' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Destino</td>
                                                         <td><input type='text' size='20' value='".htmlentities($row[destino])."' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Interno</td>
                                                         <td>
                                                             <select id='interno' name='interno'>
                                                                 <option value='$row[id_micro]'>$row[interno]</option>
                                                             </select>
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td>Km</td>
                                                         <td><input id='km' name='km' type='text' size='4' value='$row[km]' class='ui-widget ui-widget-content  ui-corner-all {required:true}'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Conductor 1</td>
                                                         <td>
                                                             <select id='chofer1' name='chofer1'>
                                                                 <option value='$row[id_chofer1]'>".htmlentities($row[chofer1])."</option>
                                                             </select>
                                                         </td>
                                                     </tr>
                                                     <tr>
                                                         <td>Conductor 2</td>
                                                         <td>
                                                             <select id='chofer2' name='chofer2'>
                                                                 <option value='$row[id_chofer2]'>".htmlentities($row[chofer2])."</option>
                                                             </select>
                                                         </td>
                                                     </tr>";

                                            $sql = "SELECT e.id_empleado, concat(apellido, ', ', nombre) as conductor
                                                    FROM tripulacionXOrdenes t
                                                    inner join empleados e on e.id_empleado = t.id_empleado
                                                    where id_orden = $_POST[orden] and id_estructura_orden = $_SESSION[structure]";

                                            $result = mysql_query($sql, $con);

                                            for ($i = 3; $i <= $cantTripulacion; $i++){
                                                $option = "<option value='0'></option>";
                                                if ($row = mysql_fetch_array($result)){
                                                    $option = "<option value='$row[0]'>".htmlentities($row[1])."</option>";
                                                }
                                                $tabla.="<tr>
                                                         <td>Conductor $i</td>
                                                         <td>
                                                             <select id='chofer$i' name='chofer$i'>
                                                                 $option
                                                             </select>
                                                         </td>
                                                     </tr>";
                                            }

                                            $tabla.="<tr>
                                                     <tr>
                                                        <td>
                                                            Observaciones
                                                        </td>
                                                        <td>
                                                            <textarea name='obs' rows='2' cols='40'>$comentario</textarea>
                                                        </td>
                                                     </tr>
                                                     <td>Finalizada</td>
                                                         <td><input type='checkbox' class='ui-widget ui-widget-content  ui-corner-all' name='finalizada' $row[finalizada] value='1'></td>
                                                     </tr>
                                                     <td>Eliminar</td>
                                                         <td><input type='checkbox' class='ui-widget ui-widget-content  ui-corner-all' name='borrada' value='1'></td>
                                                     </tr>
                                                     <tr>
                                                         <td colspan='2'><input type='button' id='close' value='Cerrar ventana'><input type='submit' id='change' value='Guardar Cambios'></td>
                                                     </tr>";
                                 }
                                 $tabla.='</table>
                                          </div>
	                                      </fieldset>
	                                      <input type="hidden" name="nroorden" value="'.$_POST[orden].'">
	                                      <input type="hidden" id="tieneasoc" name="tieneasoc" value="'.$row[tiene_asoc].'">
	                                      </form>
                                          </BODY>
                                          </HTML>';
                                 @mysql_free_result($query);
                                 @mysql_close($con);
print $tabla;
?>
