<?php
     session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
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
                 $("#change").button();
                 $("#close").button().click(function(){
                                                       $("#dialog").dialog("close");
                                                       });
                 $(":checkbox[readonly=readonly]").click(function(){
                                                                    return false;
                                                                    });


	});
	
</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.ortut {
background-color: #e6EEEE;
border: 1px solid #FFF;
font-family: fantasy;
font-size: 2pt;
padding: 4px;}

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
                       <legend class='ui-widget ui-widget-header ui-corner-all'>Orden de Trabajo N".htmlentities("°")." $_POST[orden]</legend>
                       <div id='mjw'></div>
                       <div id='tablaordenes'>
                       <table id='table'>";
                                 $con = conexcion();
                                 $sql = "SELECT date_format(o.fservicio,'%d/%m/%Y') as fsalida,
                                                date_format(fecha_regreso,'%d/%m/%Y') as fregreso,
                                                date_format(o.hsalida, '%H:%i') as hsalida,
                                                date_format(o.hllegada, '%H:%i') as hllegada,
                                                razon_social,
                                                o.nombre,
                                                upper(concat(ori.ciudad,' (',lugar_salida,')')) as lugar_salida,
                                                upper(concat(des.ciudad,' (',lugar_llegada,')')) as lugar_llegada,
                                                date_format(fecha_regreso,'%d/%m/%Y') as fregreso,
                                                date_format(ot.hora_regreso, '%H:%i') as hsalida_regreso,
                                                date_format(ot.hora_llegada_regreso, '%H:%i') as hllegada_regreso,
                                                ov.nombre as nombre_asociada,
                                                date_format(ov.fservicio,'%d/%m/%Y') as fregreso_asociada,
                                                upper(ori.ciudad) as origen,
                                                upper(des.ciudad) as destino,
                                                date_format(ov.hsalida, '%H:%i') as hsalida_asociada,
                                                date_format(ov.hllegada, '%H:%i') as hllegada_asociada,
                                                bar, banio, tv, mantas, mov_dest,
                                                ot.observaciones, capacidad_solicitada as pax
                                         FROM ordenes o
                                         inner join ciudades ori on (ori.id = o.id_ciudad_origen) and (ori.id_estructura = o.id_estructura_ciudad_origen)
                                         inner join ciudades des on (des.id = o.id_ciudad_destino) and (des.id_estructura = o.id_estructura_ciudad_destino)
                                         LEFT JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                                         INNER join ordenes_turismo ot on ot.id_orden = o.id and ot.id_estructura_orden = o.id_estructura
                                         left join ordenes_asocioadas oa on oa.id_orden = o.id
                                         left join ordenes ov on ov.id = oa.id_orden_asociada
                                         WHERE o.id = $_POST[orden]";

                                 $query = mysql_query($sql, $con) or die(mysql_error($con));
                                 $registros = mysql_num_rows($query);
				                 if($row = mysql_fetch_array($query)){
					                        $tabla.="<tr>
                                                         <td>N".htmlentities("°")." de Orden</td>
                                                         <td><input type='text' size='20' value='$_POST[orden]' readonly class='ortut ui-widget ui-widget-content  ui-corner-all'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Cliente</td>
                                                         <td><input type='text' size='30' value='".htmlentities($row[razon_social])."' class='ortut ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>";
                                            $tabla.="<tr>
                                                         <td>Nombre Servicio</td>
                                                         <td><input id='nombre' name='nombre' type='text' size='55' class='ortut ui-widget ui-widget-content  ui-corner-all' readonly value='".htmlentities($row[nombre])."'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Origen</td>
                                                         <td><input id='nombre' name='nombre' type='text' size='55' class='ortut ui-widget ui-widget-content  ui-corner-all' readonly value='".utf8_decode($row[lugar_salida])."'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Destino</td>
                                                         <td><input id='nombre' name='nombre' type='text' size='55' class='ui-widget ui-widget-content  ui-corner-all' readonly value='".utf8_decode($row[lugar_llegada])."'></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Capacidad Solicitada</td>
                                                         <td><input id='nombre' name='nombre' type='text' size='55' class='ui-widget ui-widget-content  ui-corner-all' readonly value='".$row['pax']."'></td>
                                                     </tr>                                                     
                                                     </table>
                                                     <table width='100%'>
                                                     <tr>
                                                     <td>
                                                     <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                                                               <legend class='ui-widget ui-widget-header ui-corner-all'>IDA</legend>
                                                     <table width='100%'>
                                                     <tr>
                                                         <td>Fecha de Salida</td>
                                                         <td><input id='fservicio' name='fservicio' type='text' size='20' value='$row[fsalida]' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Hora Salida</td>
                                                         <td><input id='hsalida' name='hsalida' class='hora ui-widget ui-widget-content  ui-corner-all' readonly type='text' size='5' value='$row[hsalida]'></td>
                                                     </tr>
                                                     <td>Hora Llegada</td>
                                                         <td><input id='hllegada' name='hllegada' class='hora ui-widget ui-widget-content  ui-corner-all' readonly type='text' size='5' value='$row[hllegada]'></td>
                                                     </tr>
                                                     </table>
                                                     </fieldset>
                                                     </td>
                                                     <td>
                                                     <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                                                               <legend class='ui-widget ui-widget-header ui-corner-all'>REGRESO</legend>
                                                     <table width='100%'>
                                                     <tr>
                                                         <td>Fecha de Salida</td>
                                                         <td><input id='fservicio' name='fservicio' type='text' size='20' value='$row[fregreso]' class='ui-widget ui-widget-content  ui-corner-all' readonly></td>
                                                     </tr>
                                                     <tr>
                                                         <td>Hora Salida</td>
                                                         <td><input id='hsalida' name='hsalida' class='hora ui-widget ui-widget-content  ui-corner-all' readonly type='text' size='5' value='$row[hsalida_regreso]'></td>
                                                     </tr>
                                                     <td>Hora Llegada</td>
                                                         <td><input id='hllegada' name='hllegada' class='hora ui-widget ui-widget-content  ui-corner-all' readonly type='text' size='5' value='$row[hllegada_regreso]'></td>
                                                     </tr>
                                                     </table>
                                                     </fieldset>
                                                     </td></tr></table>
                                                     <table width='100%'>
                                                     <tr><td>
                                                             <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                                                               <legend class='ui-widget ui-widget-header ui-corner-all'>Servicios a Incluir</legend>
                                                     <table width='100%'>
                                                            <tr>
                                                                <td>Bar</td>
                                                                <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($row[bar]?"checked":"")."></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Ba&ntilde;o</td>
                                                                <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($row[banio]?"checked":"")."></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Video</td>
                                                                <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($row[tv]?"checked":"")."></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Microfono</td>
                                                                <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($row[mic]?"checked":"")."></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Mantas</td>
                                                                <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($row[mantas]?"checked":"")."></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Traslados en Destino</td>
                                                                <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($row[mov_dest]?"checked":"")."></td>
                                                            </tr>
                                                     </table>
                                                     </fieldset>
                                                     </td>
                                                     <td>
                                                     <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                                                               <legend class='ui-widget ui-widget-header ui-corner-all'>Gastos a considerar</legend>
                                                     <table width='100%'>";
                                                     $sql="SELECT detalle, (select id from gastos_por_servicio_turismo gst WHERE gst.id_item_gasto = i.id and id_orden = $_POST[orden]) as ok
                                                           FROM items_gastos_turismo i";
                                                     $result = mysql_query($sql, $con) or die(mysql_error($con));
                                                     while ($data = mysql_fetch_array($result)){
                                                           $tabla.="<tr>
                                                                        <td>$data[detalle]</td>
                                                                        <td><input type='checkbox' readonly='readonly' class='ui-widget ui-widget-content  ui-corner-all' ".($data[ok]?"checked":"")."></td>
                                                                    </tr>";
                                                     }
                                                     $tabla.="<tr><td>.</td><td></td></tr><tr><td>.</td><td></td></tr>
                                                             </table></fieldset></td></tr>
                                                             <tr>
                                                                 <td colspan='2'>
                                                                     <fieldset class='ui-widget ui-widget-content ui-corner-all'>
                                                                               <legend class='ui-widget ui-widget-header ui-corner-all'>Observaciones</legend>
                                                                                       <textarea rows='4' cols='100' class='ui-widget ui-widget-content  ui-corner-all'>$row[observaciones]</textarea>
                                                                     </fieldset>
                                                                 </td>
                                                             </tr>
                                                             </table>

                                                             ";
                                            if ($registros > 1){
                                               $tabla.="<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                                                               <legend class='ui-widget ui-widget-header ui-corner-all'>Ordenes generadas automaticamente</legend>
                                                               <table class='ui-widget ui-widget-content' width='100%' align='center'>
                                                               <thead>
                                                                      <tr class='ui-widget-header'>
                                                                          <th>Fecha</th>
                                                                          <th>Servicio</th>
                                                                          <th>H. Salida</th>
                                                                          <th>H. Llegada</th>
                                                                      </tr>
                                                               </thead>
                                                               <tbody>";
                                               while ($row){
                                                     $tabla.="<tr>
                                                                  <td>$row[fregreso_asociada]</td>
                                                                  <td>$row[nombre_asociada]</td>
                                                                  <td>$row[hsalida_asociada]</td>
                                                                  <td>$row[hllegada_asociada]</td>
                                                              </tr>    ";
                                                     $row= mysql_fetch_array($query);
                                               }
                                               $tabla.="</tbody></table></fileset>";
                                            
                                            }
                                 }
                                 $tabla.='</table>
                                          </div>
	                                      </fieldset>
	                                      <input type="hidden" name="nroorden" value="'.$_POST[orden].'">
	                                      </form>
                                          </BODY>
                                          </HTML>';
                                 @mysql_free_result($query);
                                 @mysql_close($con);
print $tabla;
?>

