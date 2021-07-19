<?php
     session_start();
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
        $sql="SELECT o.id_cliente, id_ciudad_origen, lugar_salida, id_ciudad_destino,lugar_llegada,id_claseservicio,nombre,km,date_format(fservicio, '%d/%m/%Y'),
                  date_format(hsalida, '%H:%i'),date_format(hllegada, '%H:%i') ,date_format(fecha_regreso, '%d/%m/%Y'),date_format(hora_regreso, '%H:%i') ,
                  capacidad_solicitada,round(precio_venta_neto,2),round(precio_venta_final,2),round(viaticos,2),contacto,tel_contacto,mail_contacto
              FROM (SELECT * FROM ordenes WHERE id = $orden) o
              left join ordenes_turismo ot on o.id = ot.id_orden and o.id_estructura = ot.id_estructura_orden";
        $result = ejecutarSQL($sql);
        if ($data = mysql_fetch_array($result));
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
        $(".hora").mask("~9:%9",{completed:function(){}});
        $('#km, #n_interno, #pax').mask("9999");
        $('#viaticos').mask("9999.99");
        $('#preciofinal').mask("99999.99");
        $('#preciosiva').mask("99999.99",{completed:function(){$('#preciofinal').val(this.val()*1.105);}});
        <?php
             $readonly="";
             if (isset($_GET['otmo'])){
                $fservicio = dateToMysql($data[8], "/");
                $fecha = new DateTime("$fservicio");
                $hoy = new DateTime("now");
                if ($fecha <= $hoy)
                   $readonly="readonly";

                print "$('#cliente option[value=$data[0]]').attr('selected', 'selected');
                       $('#origen option[value=$data[1]]').attr('selected', 'selected');
                       $('#destino option[value=$data[3]]').attr('selected', 'selected');
                       $('#lugarsalida').val('$data[2]');
                       $('#lugarllegada').val('$data[4]');
                       $('#nombre').val('$data[6]');
                       $('#km').val('$data[7]');
                       $('#fsalida').val('$data[8]');
                       $('#hsalida').val('$data[9]');
                       $('#hllegada').val('$data[10]');
                                              $('#fregreso').val('$data[11]');
                       $('#hsalidaregreso').val('$data[12]');
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
                                                            $.post("/modelo/turismo/procesar_ordenes.php", datos, function(data) {

                                                                                                                                                    $("#mensaje").fadeIn(2000);
                                                                                                                                                    $('#mensaje').html("<div class=\"ui-widget\">"+
                                                                                                                                                                             "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                             "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                             "<strong>Se ha grabado con exito la orden en la Base de Datos</strong></p>"+
                                                                                                                                                                             "</div>"+
                                                                                                                                                                        "<div>");
                                                                                                                                                    $("#mensaje").fadeOut(2000);

                                                                                                                                                    $('#commentForm').each (function(){
                                                                                                                                                                                      this.reset();
                                                                                                                                                                                      });
                                                                                                                                                    });
                                                           }
                                  });
        $("#fservicio").focus();
	});
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
                                    <td><select id="origen" name="origen" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                        Lugar Salida <input id="lugarsalida" name="lugarsalida" class="ui-widget-content ui-corner-all" />
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Destino</td>
                                    <td><select id="destino" name="destino" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                        Legar llegada<input id="lugarllegada" name="lugarllegada" class="ui-widget-content ui-corner-all" />
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
                                    <td><input id="nombre" name="nombre" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" size="35" <?php echo "$readonly";?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Km del Servicio</td>
                                    <td colspan="2"><input id="km" size="4" name="km" class="number ui-widget-content  ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Datos Salida</td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Salida</label></td>
                                    <td><input id="fsalida" name="fsalida" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" <?php echo "$readonly";?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><input id="hsalida" maxlength="5" size="4" name="hsalida" class="required hora ui-widget-content ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Llegada</td>
                                    <td colspan="2"><input id="hllegada" maxlength="5" size="4" name="hllegada" class="hora ui-widget-content ui-corner-all" <?php echo "$readonly";?>/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Datos Regreso</td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Regreso</label></td>
                                    <td><input id="fregreso" name="fregreso" class="ui-widget ui-widget-content  ui-corner-all" minlength="2" <?php echo "$readonly";?>/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><input id="hsalidaregreso" maxlength="5" size="4" name="hsalidaregreso" class="hora ui-widget-content ui-corner-all" <?php echo "$readonly";?> /></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Capacidad Solicitada</td>
                                    <td colspan="2"><input id="pax" size="4" name="pax" class="required ui-widget-content number  ui-corner-all"/></td>
                                </tr>


                                <tr>
                                    <td WIDTH="20%">Precio Sin Iva</td>
                                    <td colspan="2"><input id="preciosiva" size="4" name="preciosiva" class="required number ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Precio Final</td>
                                    <td colspan="2"><input id="preciofinal" size="4" name="preciofinal" class="required number ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Viaticos</td>
                                    <td colspan="2"><input id="viaticos" size="4" name="viaticos" class="required number ui-widget-content ui-corner-all" /></td>
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
                     <input type="hidden" name="orden" id="orden" value="'.$_GET['otmo'].'"/>';
    ?>
</form>
</div>

         

</BODY>
</HTML>
