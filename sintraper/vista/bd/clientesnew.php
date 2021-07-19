<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, 1);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>

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

#commentForm .error{
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
                                                        $("#commentForm").validate({
		                                                                            submitHandler: function(){
		                                                                                                      var datos = $("#commentForm").serialize();
                                                                                                              $.post("/modelo/procesa/procesar_bd.php", datos, function(data){var mje = "<div class=\"ui-widget\">"+
                                                                                                                                                                                        "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                                                        "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                                                        "<strong>Se ha grabado con exito el cliente en la Base de Datos!</strong></p>"+
                                                                                                                                                                                        "</div>"+
                                                                                                                                                                                        "<div>";
                                                                                                                                                                                        $('#restx').html(mje);
                                                                                                                                                                                        $( "#contacts tbody").html('');
                                                                                                                                                                                        num = 1;
                                                                                                                                                                                        $('#commentForm').each (function(){
                                                                                                                                                                                                                        this.reset();
                                                                                                                                                                                        });
                                                                                                                                                                              }).fail(function() { alert("No se ha podido agregar e cliente a la Base de Datos"); });

                                                                                                              }
                                                                                   });
                                                        $('#create-client').button();
                                                       });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm">
               <div id ="restx"></div>
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Alta de Cliente</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td WIDTH="20%"><label for="razon">Razon Social</label></td>
                                    <td><input id="razon" name="razon" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td><input id="telefono" name="telefono" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">E-mail</td>
                                    <td><input id="email" name="email" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Direccion</td>
                                    <td><input id="direccion" name="direccion" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
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
                                    <td WIDTH="20%">Responsabilidad ante el IVA</td>
                                    <td><select id="resp-iva" name="resp-iva" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('responsabilidadiva', 'responsabilidad', 'id', 'responsabilidad', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">CUIT</td>
                                    <td><input id="cuit" name="cuit" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        Contactos:
                                    </td>
                                    <td>&nbsp;</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                         <div id="users-contain" class="ui-widget">
                                         <form id="contactos">
	                                          <table id="contacts" class="ui-widget ui-widget-content">
		                                             <thead>
			                                                <tr class="ui-widget-header ">
				                                                <th>Nombre</th>
				                                                <th>Tel. Fijo</th>
				                                                <th>Tel. Movil</th>
				                                                <th>Email</th>
				                                                <th>Cargo</th>
			                                                 </tr>
	                                                 </thead>
		                                             <tbody>
		                                             </tbody>
                                              </table>
                                           </form>
                                              <input type="button" id="create-contact" value="Agregar un contacto" align="right"/>
                                         </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input type="submit" id="create-client" value="Guardar Cliente"/> </td>
                                </tr>
                         </table>
            </fieldset>
         </form>
         <div id="dialog-form" title="Crear un nuevo contacto">
              <form id="upcontact">
	                <fieldset>
		                      <label for="name">Nombre</label>
		                      <span></span>
		                      <input type="text" name="name" id="name" class="text ui-widget-content ui-corner-all {required:true}" />
		                      <label for="password">Telefono fijo</label>
		                      <span></span>
		                      <input type="text" name="t_fijo" id="t_fijo" value="" class="text ui-widget-content ui-corner-all {required:true}"  />
		                      <label for="password">Telefono movil</label>
		                      <span></span>
		                      <input type="text" name="t_movil" id="t_movil" value="" class="text ui-widget-content ui-corner-all {required:true}"  />
		                      <label for="email">Email</label>
		                      <span></span>
		                      <input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all {email:true}"  />
		                      <label for="password">Cargo</label>
		                      <span></span>
		                      <input type="text" name="cargo" id="cargo" value="" class="text ui-widget-content ui-corner-all "  />
                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Continuar" name="envioFormulario">
                    </fieldset>
              </form>
         </div>
</body>
</html>

