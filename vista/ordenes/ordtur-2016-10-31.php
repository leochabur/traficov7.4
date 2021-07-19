<?php
     session_start();
     set_time_limit(0);
     error_reporting(0);
  date_default_timezone_set('America/New_York');
   /*  include('../../modelo/provincia.php');*/
     include('../../modelo/utils/dateutils.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     include_once('../../controlador/ejecutar_sql.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');

     if (isset($_GET['otmo'])){
        $orden = $_GET['otmo'];
        $sql="SELECT o.id_cliente, id_ciudad_origen, lugar_salida, id_ciudad_destino,lugar_llegada,
                     id_claseservicio,nombre,km,date_format(fservicio, '%d/%m/%Y') as fservicio,
                  date_format(hsalida, '%H:%i'),date_format(hllegada, '%H:%i') ,
                  date_format(fecha_regreso, '%d/%m/%Y'),
                  date_format(hora_regreso, '%H:%i') ,
                  capacidad_solicitada,round(precio_venta_neto,2),round(precio_venta_final,2),round(viaticos,2),contacto,tel_contacto,mail_contacto,
                  bar, banio, tv, mantas, microfono, mov_dest, date_format(hora_llegada_regreso, '%H:%i') as llegaregreso,
                  fservicio as fecha_orden
              FROM (SELECT * FROM ordenes WHERE id = $orden) o
              left join ordenes_turismo ot on o.id = ot.id_orden and o.id_estructura = ot.id_estructura_orden";
        $result = ejecutarSQL($sql);
        if ($data = mysql_fetch_array($result));

        $fservicio = new DateTime("$data[fecha_orden]");
        $ahora = new DateTime("now");

     }
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
	$(function() {

        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("input:button").button({icons: {
                                       primary: "ui-icon-disk"
                                       }});
        $('#envioFormulario').button();
        $(':button').button();
        $(':submit').button();

		$.mask.definitions['~']='[012]';
        $.mask.definitions['%']='[012345]';
        $(".hora").mask("~9:%9");

        $('#km, #n_interno, #pax').mask("9999");
        $('#viaticos').mask("9999.99");
        $('#preciofinal').mask("99999.99");
        $('#preciosiva').mask("99999.99");
        <?php
             $readonly="";
             $edit=false;
             $yarealizado=0;
             if (isset($_GET['otmo'])){
                $edit=true;
                if ($fservicio <= $ahora){
                   $readonly="readonly";
                   $yarealizado=1;//flag para indicar que la orden que se esta modificando correpsonde a un servicio que ya ha sido realizado
                   $required="";
                   print "$(':checkbox').click(function(){ return false;});";
                }
                else{
                     $required="required";
                     print '$(".semper").click(function(){ return false; });';
                }

                print "$('#cliente option[value=$data[0]]').attr('selected', 'selected');
                       $('#origen option[value=$data[1]]').attr('selected', 'selected');
                       $('#destino option[value=$data[3]]').attr('selected', 'selected');
                       $('#lugarsalida').val('$data[2]');
                       $('#lugarllegada').val('$data[4]');
                       $('#nombre').val('$data[6]');
                       $('#km').val('$data[7]');
                       $('#fsalida').val('$data[fservicio]');
                       $('#hsalida').val('$data[9]');
                       $('#hllegada').val('$data[10]');
                                              $('#fregreso').val('$data[11]');
                       $('#hsalidaregreso').val('$data[12]');
                       $('#hllegadaregreso').val('$data[llegaregreso]');
                       $('#pax').val('$data[13]');
                       $('#preciosiva').val('$data[14]');
                                              $('#preciofinal').val('$data[15]');
                       $('#viaticos').val('$data[16]');
                                              $('#nomcontacto').val('$data[17]');
                                              $('#telcontacto').val('$data[18]');
                       $('#mailcontacto').val('$data[19]');";
             }
             
             if ($readonly !="readonly")
             print "$(\"#fsalida, #fregreso\").datepicker({
                                    dateFormat : 'dd/mm/yy',
                                    autoOpen: true
                                    });";
        ?>
        $('#origen, #destino, #cliente').selectmenu({width: 350});
        $('#clase').selectmenu({width: 250});
        

                                               

        $('#commentForm').validate({
                                  submitHandler: function(){
                                                            var datos = $("#commentForm").serialize();
                                                            $("#envioFormulario").hide();
                                                            $.post("/modelo/turismo/procesar_ordenes.php", datos, function(data) {
                                                                                                                                 var response = $.parseJSON(data);
                                                                                                                                 alert(response.sql);
                                                                                                                                 alert(response.msge);
                                                                                                                                  if (response.status){
                                                                                                                                     $('#commentForm')[0].reset();
                                                                                                                                  }
                                                                                                                                  else{
                                                                                                                                       alert(response.msge);
                                                                                                                                  }
                                                                                                                                  $("#envioFormulario").show();
                                                                                                                                  });

                                                           }
                                  });
        $("#fservicio").focus();
        $('.chng').change(function(){setName();});
        	setName();
	});
	
	function setName(){
          $('#nombre').val($('#clase option:selected').text()+' ('+$('#origen option:selected').text()+' - '+$('#destino option:selected').text()+')');
	}

	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 150px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
#commentForm td{padding: 2px;}
#commentForm #upuda .error{
	font-size:0.8em;
	color:#ff0000;
}
#upuda .error{
	font-size:0.8em;
	color:#ff0000;
}

#updri .error{
	font-size:0.8em;
	color:#ff0000;
}

#commentForm .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<BODY>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm" method="get" action="" name="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Crear Orden Turismo</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="75%">
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td><select id="cliente" name="cliente" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Origen</td>
                                    <td><select id="origen" name="origen" title="Please select something!"  class="chng ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                        Lugar Salida <input id="lugarsalida" name="lugarsalida" class="<?php echo $required;?>ui-widget-content ui-corner-all" />
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Destino</td>
                                    <td><select id="destino" name="destino" title="Please select something!"  class="chng ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                        Legar llegada<input id="lugarllegada" name="lugarllegada" class="<?php echo $required;?>ui-widget-content ui-corner-all" />
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Clase Servicio</td>
                                    <td><select id="clase" name="clase"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('claseservicio', 'clase', 'id', 'clase', "(id_estructura = $_SESSION[structure]) and (id = 4)");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Nombre Servicio</label></td>
                                    <td><input id="nombre" name="nombre" class="<?php echo $required;?> ui-widget ui-widget-content  ui-corner-all" minlength="2" size="75" <?php echo "$readonly";?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Km del Servicio</td>
                                    <td colspan="2"><input id="km" size="4" name="km" class="<?php echo $required;?> number ui-widget-content  ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Datos Salida</td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Salida</label></td>
                                    <td><input id="fsalida" name="fsalida" class="<?php echo $required;?> ui-widget ui-widget-content  ui-corner-all" minlength="2" <?php echo "$readonly";?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><input id="hsalida" maxlength="5" size="4" name="hsalida" class="<?php echo $required;?> hora ui-widget-content ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Llegada</td>
                                    <td colspan="2"><input id="hllegada" maxlength="5" size="4" name="hllegada" class="<?php echo $required;?> hora ui-widget-content ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Datos Regreso</td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Regreso</label></td>
                                    <td><input id="fregreso" name="fregreso" class="<?php echo $required;?> ui-widget ui-widget-content  ui-corner-all" minlength="2" <?php echo "$readonly";?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><input id="hsalidaregreso" maxlength="5" size="4" name="hsalidaregreso" class="<?php echo $required;?> hora ui-widget-content ui-corner-all" <?php echo "$readonly";?> /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Llegada</td>
                                    <td colspan="2"><input id="hllegadaregreso" maxlength="5" size="4" name="hllegadaregreso" class="<?php echo $required;?> hora ui-widget-content ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Capacidad Solicitada</td>
                                    <td colspan="2"><input id="pax" size="4" name="pax" class="<?php echo $required;?> ui-widget-content number  ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Servicios Solicitados</td>
                                    <td colspan="2">Ba&ntilde;o<input type="checkbox" name="banio" <?php echo ($edit&&$data[banio])?"checked":"";?> class="<?php ?>">Bar<input type="checkbox" name="bar" <?php echo ($edit&&$data[bar])?"checked":""; ?>>DVD<input type="checkbox" name="dvd" <?php echo ($edit&&$data[tv])?"checked":""; ?>>Microfono<input type="checkbox" name="mic" <?php echo ($edit&&$data[microfono])?"checked":""; ?>>Mantas<input type="checkbox" name="mantas" <?php echo ($edit&&$data[mantas])?"checked":""; ?>>Excursiones en Destino<input type="checkbox" name="excur" <?php echo ($edit&&$data[mov_dest])?"checked":""; ?>></td>
                                </tr>

                                <!--tr>
                                    <td WIDTH="20%">Precio Sin Iva</td>
                                    <td colspan="2"><input checked name="price" type="radio" value="neto"><input id="preciosiva" size="4" name="preciosiva" class="number ui-widget-content ui-corner-all" /></td>

                                </tr-->
                                <tr>
                                    <td WIDTH="20%">Precio Final (IVA Incluido)</td>
                                    <td colspan="2"><input id="preciofinal" size="4" name="preciofinal" class="<?php echo $yarealizado?"required":"";?> number ui-widget-content ui-corner-all"/><input name="efc" id="efc" type="checkbox"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Pago Anticipado Obligatorio</td>
                                    <td colspan="2"><input type="checkbox" name="pagoanti" class="ui-widget-content ui-corner-all"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Viaticos</td>
                                    <td colspan="2"><input id="viaticos" size="4" name="viaticos" class="number ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td>Gastos a Cargo de la Empresa</td>
                                    <td>
                                        <?php
                                             if (isset($_GET['otmo']))
                                                $edit=", (SELECT id FROM gastos_por_servicio_turismo where id_item_gasto = i.id and id_orden = $_GET[otmo]) as ok";
                                             $sql = "SELECT id, detalle, requiere $edit
                                                     FROM items_gastos_turismo i
                                                     order by detalle";
                                             $result = ejecutarSQL($sql, $conn);
                                             while ($row = mysql_fetch_array($result)){
                                                   $chk = ($row[ok]?"checked":"");
                                                   if ($row[requiere]){
                                                      $chk="checked class='semper'";
                                                   }
                                                   print "$row[detalle]<input type='checkbox' id='gas$row[0]' name='gas$row[0]' $chk>";
                                             }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Observaciones</td>
                                    <td><textarea rows="5" cols="45" class="ui-widget-content ui-corner-all" name="observa"></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Datos de contacto</td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nombre</td>
                                    <td colspan="2"><input id="nomcontacto" name="nomcontacto" class="ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td colspan="2"><input id="telcontacto" name="telcontacto" class="ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">E-mail</td>
                                    <td colspan="2"><input id="mailcontacto" name="mailcontacto" class="ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                <?php
                                     if (!isset($_GET['otmo'])){
                                ?>
                                    <td colspan="3" align="right"><input id="envioFormulario" class="boton" type="submit" value="Guardar Orden" name="envioFormulario"></td>
                                <?php
                                     }
                                     else{
                                ?>
                                          <td colspan="3" align="right"><input id="envioFormulario" class="boton" type="submit" value="Modificar Orden" name="envioFormulario"></td>
                                <?php
                                     }
                                ?>
                                </tr>
                                
                         </table>
	</fieldset>
	<?php
         if (!isset($_GET['otmo']))
	        print '<input type="hidden" name="accion" id="accion" value="sortur"/>';
          else
              print '<input type="hidden" name="accion" id="accion" value="mortur"/>
                     <input type="hidden" name="realizada" id="realizada" value="'.$yarealizado.'"/>
                     <input type="hidden" name="orden" id="orden" value="'.$_GET['otmo'].'"/>';
    ?>
</form>
</div>

         

</BODY>
</HTML>
