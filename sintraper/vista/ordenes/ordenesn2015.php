<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');*/
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
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
		$("#fservicio").datepicker({
                                    dateFormat : 'dd/mm/yy',
                                    autoOpen: true,
                                    onSelect: function(date) {
                                                                  $.post("cargar_combo_conductores.php", {fecha: date}, function(data){
                                                                                                                                    $("#conductor").empty();
                                                                                                                                    $("#conductor").append(data);
                                                                                                                                    $('#conductor').selectmenu({width: 450});
                                                                                                                                    });
                                                             }
                                   });
		$.mask.definitions['~']='[012]';
        $.mask.definitions['%']='[012345]';
        $(".hora").mask("~9:%9",{completed:function(){}});
        $('#km, #n_interno').mask("9999");
        $('#origen, #destino, #cliente').selectmenu({width: 350});
        $('#interno').selectmenu({width: 100});
        $('#conductor').selectmenu({width: 450});
        $("#addinterno").button().click(function(){
                                                      $( "#udaform" ).dialog( "open" );
                                               });
        $("#addcond").button().click(function(){
                                                      $( "#driform" ).dialog( "open" );
                                               });
                                               

        $('#udaform').dialog({autoOpen: false,
                              height: 370,
                              width: 480,
                              modal: true,
                              close: function(){
                                                $('#upuda input:text').val('');
                                                }
                              });
        $('#driform').dialog({autoOpen: false,
                              height: 250,
                              width: 450,
                              modal: true,
                              close: function(){
                                                $('#upuda input:text').val('');
                                                }
                              });
        $('#upuda').validate({
                                  submitHandler: function(){
                                                            alert($('#upuda').serialize());
                                                            $.post("/modelo/segvial/altauda.php", $('#upuda').serialize(), function(data) { alert(data);
                                                                                                                                                                     if(data == '0'){
                                                                                                                                                                              alert("N&deg; Interno existente en la Base de Datos");

                                                                                                                                                                     }
                                                                                                                                                                     else{
                                                                                                                                                                          $('#interno').append("<option value='"+data+"' selected='selected'>"+$('#n_interno').val()+"</option>");
                                                                                                                                                                          $('#interno').selectmenu({width: 350});
                                                                                                                                                                          $('#upuda').each (function(){
                                                                                                                                                                                                            this.reset();
                                                                                                                                                                                                       });
                                                                                                                                                                          $('#udaform').dialog('close');

                                                                                                                                                                     }
                                                                                                                                                                     });
                                                            }
                             });
        $('#updri').validate({
                                  submitHandler: function(){
                                                            $.post("/modelo/rrhh/altacond.php", $('#updri').serialize(), function(data) {
                                                                                                                                             if(data == 0){
                                                                                                                                                     alert("N&deg; Legajo existente en la Base de Datos");

                                                                                                                                             }
                                                                                                                                             else{
                                                                                                                                                     $('#conductor').append("<option value='"+data+"' selected='selected'>"+$('#apellido').val()+", "+$('#nombre_c').val()+"</option>");
                                                                                                                                                     $('#conductor').selectmenu({width: 350});
                                                                                                                                                     $('#updri').each (function(){
                                                                                                                                                                                        this.reset();
                                                                                                                                                                                 });
                                                                                                                                                     $('#driform').dialog('close');
                                                                                                                                             }
                                                                                                                                             });
                                                            }
                             });
        $('#commentForm').validate({
                                  submitHandler: function(){
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

                                                                                                                                                    $('#commentForm :text').each (function(){
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Crear Orden Eventual</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Servicio</label></td>
                                    <td><input id="fservicio" name="fservicio" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Nombre Servicio</label></td>
                                    <td><input id="nombre" name="nombre" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
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
                                    </td>
                                    <td>
                                        <input type="button" value="+">
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Destino</td>
                                    <td><select id="destino" name="destino" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="button" value="+">
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Clase Servicio</td>
                                    <td><select id="clase" name="clase"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('claseservicio', 'clase', 'id', 'clase', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Conductor</td>
                                    <td><select id="conductor" name="conductor" class="ui-widget-content  ui-corner-all">
                                                <option value="0"></option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="button" value="+" id="addcond">
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Interno</td>
                                    <td><select id="interno" name="interno" class="ui-widget-content  ui-corner-all">
                                                <option value="0"></option>
                                                <?php
                                                     armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "(id_estructura = ".$_SESSION['structure'].")");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input id="addinterno" type="button" value="+">
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Citacion</td>
                                    <td colspan="2"><input id="hcitacion" maxlength="5" size="4" name="hcitacion" class="required hora ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><input id="hsalida" maxlength="5" size="4" name="hsalida" class="required hora ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Llegada</td>
                                    <td colspan="2"><input id="hllegada" maxlength="5" size="4" name="hllegada" class="required hora ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Fin Servicio</td>
                                    <td colspan="2"><input id="hfins" maxlength="5" size="4" name="hfins" class="required hora ui-widget-content ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Km del Servicio</td>
                                    <td colspan="2"><input id="km" size="4" name="km" class="required ui-widget-content number  ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input id="envioFormulario" class="boton" type="submit" value="Guardar Orden" name="envioFormulario"></td>
                                </tr>
                                
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="orev"/>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>
         <div id="udaform" title="Nueva unidad">
              <form id="upuda">
	                <fieldset>
                              <label for="name">Empleador</label>
                              <select id="propietario" name="propietario" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('empleadores', 'razon_social', 'id', 'razon_social', "(id_estructura = ".STRUCTURED.")");
                                                ?>
                              </select>
                              <br> <br>
		                      <label for="name">N&deg; Interno</label><br>
		                      <input type="text" name="n_interno" id="n_interno" size="4" class="text ui-widget-content ui-corner-all required" />
                              <br><br>
                              <label for="name">Tipo Unidad</label>
		                      <select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('tipounidad', 'tipo', 'id', 'tipo', "(id_estructura = ".STRUCTURED.")");
                                                ?>
                              </select>
                              <br><br>
                              <label for="name">Cant. Asientos</label>    <br>
                              <input id="cantas" name="cantas" size="2" class="ui-widget ui-widget-content ui-corner-all" minlength="2"/>

                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Guardar Unidad" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" name="accion" id="accion" value="sveevt"/>
              </form>
         </div>
         
         <div id="driform" title="Nuevo Conductor">
              <form id="updri">
	                <fieldset>
                              <label for="name">Legajo</label>
		                      <input type="text" name="legajo" id="legajo" size="4" class="text ui-widget-content ui-corner-all required" /> <BR>
		                      <label for="name">Apellido</label>
		                      <input type="text" name="apellido" id="apellido" size="25" class="text ui-widget-content ui-corner-all required" /><BR>
		                      <label for="name">Nombre</label>
		                      <input type="text" name="nombre" id="nombre_c" size="25" class="text ui-widget-content ui-corner-all required" />
                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Guardar Conductor" name="envioFormulario">
                    </fieldset>
                    <input type="hidden" value="sveevt" name="accion">
              </form>
         </div>
</BODY>
</HTML>
