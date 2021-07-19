<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '/nuevotrafico');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
.button-small{font-size: 62.5%;}
div#users-contain {margin: 20px 0; font-size: 62.5%;}
div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
input.text { margin-bottom:12px; width:95%; padding: .4em; }
#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       var num = 1;
                                                       $("#cuit").mask("99-99999999-9"),
                                                       $("#newcity").button();
                                                       $('#envioFormulario').button();
                                                       $('.fecha').datepicker();
                                                       $("#create-contact").button().click(function() {
				                                                                                      $( "#dialog-form" ).dialog( "open" );
			                                                                                          });
                                                       $('select').selectmenu({style:'popup', width: 350});
                                                       $( "#dialog-form" ).dialog({
			                                                                       autoOpen: false,
                                                                                   height: 385,
			                                                                       width: 350,
			                                                                       modal: true,
			                                                                       close: function(){
                                                                                                     $('#upcontact input:text').val('');
                                                                                                     $('#upcontact span').val('');
			                                                                                         }
                                                                                   });
                                                                                   
                                                       	$('#upcontact').validate({
		                                                                         submitHandler: function(){
			                                                                                               str = $("#formAjax").serialize();
                                                                                                           $( "#contacts tbody" ).append( "<tr>" +
							                                                                                                              "<td><input name=\"name_contact_"+num+"\" type='text' value=\"" + $("#name").val() + "\"></td>" +
							                                                                                                              "<td><input name=\"t_fijo_contact_"+num+"\" type='text' value=\"" + $("#t_fijo").val() + "\"></td>" +
							                                                                                                              "<td><input name=\"t_movil_contact_"+num+"\" type='text' value=\"" + $("#t_movil").val()+ "\"></td>" +
							                                                                                                              "<td><input name=\"email_contact_"+num+"\" type='text' value=\"" + $("#email").val() + "\"></td>" +
							                                                                                                              "<td><input name=\"cargo_contact_"+num+"\" type='text' value=\"" + $("#cargo").val() + "\"></td>" +
						                                                                                                                "</tr>" );
                                                                                                           num++;
                                                                                                           $('#dialog-form').dialog('close');
                                                                                 },
		                                                                         errorPlacement: function(error, element) {
			                                                                                     error.appendTo(element.prev("span").append());
                                                                                 }
                                                                                 });
                                                        $("#commentForm").validate();
                                                        $('#create-client').button().click(function(){
                                                                                                      var datos = $("#commentForm").serialize();
                                                                                                      $.post("/nuevotrafico/modelo/procesa/procesar_bd.php", datos, function(data) {});
                                                                                                      $( "#contacts tbody").html('');
                                                                                                      num = 1;
                                                                                                      });
                                                       });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta de Personal</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%">Empleador</td>
                                    <td><select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('empleadores', 'razon_social', 'id', 'razon_social', "(id_estructura = ".STRUCTURED.")");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Legajo</label></td>
                                    <td><input id="legajo" name="legajo" size="8" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Apellido</td>
                                    <td><input id="telefono" name="telefono" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nombre</td>
                                    <td><input id="email" name="email" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Direccion</td>
                                    <td><input id="direccion" name="direccion" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td><input id="direccion" name="direccion" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Localidad</td>
                                    <td><select id="ciudad" name="ciudad" title="Please select something!"  class="ui-widget ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Nacimiento</td>
                                    <td><input id="fnac" name="fnac" class="required ui-widget ui-widget-content  ui-corner-all fecha" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">F. Inicio Relacion Laboral</td>
                                    <td><input id="fini" name="fini" class="required ui-widget ui-widget-content  ui-corner-all fecha" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">CUIL</td>
                                    <td><input id="cuit" name="cuit" class="{required:true} ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="button" id="create-client" value="Guardar Cliente"/> </td>
                                </tr>
                         </table>
            </fieldset>
         </form>
</body>
</html>

