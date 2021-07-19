<?php
     session_start();
     include('../../controlador/ejecutar_sql.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     $vacio = getOpcion('cliente-vacio', $_SESSION['structure']);
     define(VACIO, $vacio);
     define(RAIZ, '');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script>
	$(function() {
        $('select').selectmenu({style:'popup',
                                width: 250});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("button").button({icons: {
                                       primary: "ui-icon-disk"
                                       }});
		$("#fservicio").datepicker({dateFormat:'dd/mm/yy'});
		$.mask.definitions['~']='[012]';
        $.mask.definitions['%']='[012345]';
        $(".hora").mask("~9:%9",{completed:function(){}});
        $(".number").mask("9999");
       	$('#commentForm').validate({
                                   rules: {
                                           fservicio: "required",
                                           nombre: "required",
                                           km: { required: true, number: true }
                                           },
                                   messages: {
                                             fservicio: "La fecha es obligatoria!!",
                                             nombre: "Se requiere un nombre para el servicio!!",
                                             km: { required: 'Se requieren los km del servicio!!',
                                                   number: 'El valor del campo debe ser numerico!!'
                                                  }
                                             },
                                   submitHandler: function(form){
                                                                 var datos = $("#commentForm").serialize();

                                                                 $.post("/modelo/procesa/procesar_ordenes.php", datos, function(data) {
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
	});
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

form table td{
  padding-top: 2px;
  padding-right: 1px;
  padding-bottom: 2px;
  padding-left: 1px;
}
label.error { float: none; color: red; padding-left: .5em; vertical-align: top; }
</style>
<BODY>
<?php
     menu();
     if (!$vacio){
        print "<br>No ha configurado su sistema con un cliente por defecto para los servicios vacios!!!";
        exit();
     }
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm" method="post" action="">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Crear Vacio Eventual</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="65%">
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Servicio</label></td>

                                    <td><input id="fservicio" name="fservicio" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="nombre">Nombre Servicio</label></td>
                                    <td><span></span><input id="nombre" name="nombre" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td><span></span></td>
                                </tr>
                                <tr>
                                    <td>Cliente</td>
                                    <td><input id="" name="cliente" type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all" value="Master Bus S.A." readonly="readonly"></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Correspondiente a:</td>
                                    <td><select id="corresponde_a" name="corresponde_a" title="Please select something!" >
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
                                    <td><select id="origen" name="origen" title="Please select something!"  class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Destino</td>
                                    <td><select id="destino" name="destino" title="Please select something!"  class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Citacion</td>
                                    <td colspan="2"><span></span><input id="hcitacion" size="5" name="hcitacion" class="hora ui-widget ui-widget-content  ui-corner-all required" /></td>
                                </tr>
                                                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><span></span><input id="hsalida" size="5" name="hsalida" class="hora ui-widget ui-widget-content  ui-corner-all required" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Finalizacion</td>
                                    <td colspan="2"><span></span><input id="hfin" size="5" name="hfin" class="hora ui-widget ui-widget-content  ui-corner-all required" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Fin Servicio</td>
                                    <td colspan="2"><span></span><input id="hfins" size="5" name="hfins" class="hora ui-widget ui-widget-content  ui-corner-all required" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Km del Servicio</td>
                                    <td colspan="2"><span></span><input id="km" size="4" name="km" class="ui-widget ui-widget-content  ui-corner-all number required" /></td>
                                </tr>
                                <tr>

                                    <td colspan="3" align="right"><hr align="tr"><button class="ui-button ui-state-default">Guardar Servicio</button></td>
                                </tr>
                         </table>
	</fieldset>
	<input type="hidden" name="cliente_vacio" id="cliente_vacio" value="<?echo VACIO;?>"/>
	<input type="hidden" name="accion" id="accion" value="soe"/>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

</BODY>
</HTML>
