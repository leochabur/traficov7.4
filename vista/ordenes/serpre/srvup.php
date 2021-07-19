<?php
     session_start();

     define(RAIZ, '');
     include('../../../vista/main.php');
     include('../../../vista/paneles/viewpanel.php');
     include_once('../../../controlador/ejecutar_sql.php');
     $vacio = getOpcion('cliente-vacio', $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
     function selectTurnos($value, $option){
              print "<select id=\"turno\" name=\"turno\"  class=\"ui-widget-content  ui-corner-all\">";
              armarSelect('turnos', 'turno', 'id', 'turno', "(id_estructura = $_SESSION[structure])");
              print '</select>';
     }
     
     function selectTipo($value, $option){
              print '<select id="tipo" name="tipo"  class="ui-widget-content  ui-corner-all"  validate="required:true">';
              armarSelect('tiposervicio', 'tipo', 'id', 'tipo', "(id_estructura = $_SESSION[structure])");
              print '</select>';
     }
     
     function selectIV($value, $option){
              print '<select id="iv" name="iv"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                             <option value="i">IDA</option>
                             <option value="v">VUELTA</option>
                     </select>';
     }
     



?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script>
	$(function() {

         $('select').selectmenu({width: 400});
        var num = 1;
        $('#cliempty').toggle();
        $(':radio').change(function(){
                                      $('#cliempty').toggle();
                                      if($(this).val() == 1){
                                           $('#cliente').attr('disabled', 'enabled');
                                      }
                                      else{
                                           $('#cliente').removeAttr('disabled');
                                      }
                                      $("#cliente option[value=<?print $vacio;?>]").attr("selected",true);
                                      $('#cliente').selectmenu({width: 400});
                                      });
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("button").button();
		$('#envioFormulario').button();
		$("#fservicio").datepicker();
        $.mask.definitions['H']='[0123456789]';
        $.mask.definitions['N']='[012345]';
        $.mask.definitions['n']='[0123456789]';
        $(".hora").mask("Hn:Nn");
        $(".number").mask("9999");
        $('#savesrv').button();
        $( "#dialog-form" ).dialog({
                                    autoOpen: false,
                                    height: 385,
                                    width: 620,
                                    modal: true,
                                    close: function(){
                                                      $('#upcontact input:text').val('');
                                                      }
                                    });
        $("#add-srv").button().click(function() {
                                                       $( "#dialog-form" ).dialog( "open" );
                                                       });
                                                       
       	$('#upcontact').validate({
                                  submitHandler: function(){
                                                            $( "#contacts tbody" ).append( "<tr>" +
                                                                                           "<td><input class=\"hora\" maxlength=\"5\" size=\"5\" name=\"h_cita_"+num+"\" type=\"text\" value=\"" + $("#h_cita").val() + "\"></td>" +
                                                                                           "<td><input class=\"hora\" maxlength=\"5\" size=\"5\" name=\"h_salida_"+num+"\" type='text' value=\"" + $("#h_salida").val() + "\"></td>" +
                                                                                           "<td><input class=\"hora\" maxlength=\"5\" size=\"5\" name=\"h_llegada_"+num+"\" type='text' value=\"" + $("#h_llegada").val()+ "\"></td>" +
                                                                                           "<td><input class=\"hora\" maxlength=\"5\" size=\"5\" name=\"h_fin_"+num+"\" type='text' value=\"" + $("#h_fin").val() + "\"></td>" +
                                                                                           "<td><input type='text' value=\"" + $("#turno option:selected").text() + "\"></td>" +
                                                                                           "<td><input type='text' value=\"" + $("#tipo option:selected").text() + "\"></td>" +
                                                                                           "<td><input centro\" type='text' value=\"" + $("#iv option:selected").text() + "\"></td>"+
                                                                                           "<input type=\"hidden\" name=\"turno_"+num+"\" value=\""+$("#turno option:selected").val()+"\">"+
                                                                                           "<input type=\"hidden\" name=\"tipo_"+num+"\" value=\""+$("#tipo option:selected").val()+"\">"+
                                                                                           "<input type=\"hidden\" name=\"iv_"+num+"\" value=\""+$("#iv option:selected").val()+"\">"+
                                                                                           "</tr>" );
                                                            num++;
                                                            $('#dialog-form').dialog('close');
                                                            },
                                  errorPlacement: function(error, element) {
                                                                           error.appendTo(element.next("span").append());
                                                                           }
                                  });
        $('#upnwserv').validate({
                                  submitHandler: function(){
                                                            var datos = $("#upnwserv").serialize();
                                                            $.post("/modelo/procesa/servicios/newsrv.php", datos, function(data) {
                                                                                                                                                var mje;
                                                                                                                                                if (data){
                                                                                                                                                   mje = "<div class=\"ui-widget\">"+
                                                                                                                                                         "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                         "<p><span class=\"ui-icon ui-icon-info\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                         "<strong>Se ha grabado con exito el servicio en la Base de Datos!</strong></p>"+
                                                                                                                                                         "</div>"+
                                                                                                                                                         "<div>";
                                                                                                                                                }
                                                                                                                                                else{
                                                                                                                                                   mje = "<div class=\"ui-widget\">"+
                                                                                                                                                         "<div class=\"ui-state-highlight ui-corner-all\" style=\"margin-top: 20px; padding: 0 .7em;\">"+
                                                                                                                                                         "<p><span class=\"ui-icon ui-icon-alert\" style=\"float: left; margin-right: .3em;\"></span>"+
                                                                                                                                                         "<strong>Se han producido errores al intentar guardar el servicio</strong></p>"+
                                                                                                                                                         "</div>"+
                                                                                                                                                         "<div>";
                                                                                                                                                }

                                                                                                                                                $("#mensaje").fadeIn(2000);
                                                                                                                                                $('#mensaje').html(mje);
                                                                                                                                                $("#mensaje").fadeOut(2000);
                                                                                                                                                if (data){
                                                                                                                                                          $('#upnwserv').each (function(){
                                                                                                                                                                                             this.reset();
                                                                                                                                                                                             });
                                                                                                                                                          $( "#contacts tbody").html('');
                                                                                                                                                          $('#cliempty').hide();
                                                                                                                                                          $('#nookemp').attr('checked', true);
                                                                                                                                                          $('#cliente').removeAttr('disabled');
                                                                                                                                                          $('#cliente').selectmenu({width: 400});
                                                                                                                                                          num = 1;
                                                                                                                                                }
                                                                                                                                                });
                                                           },
                                  errorPlacement: function(error, element) {
                                                                           error.appendTo(element.next("span").append());
                                                                           }
                                  });
         $('#origen').change(function(data){
                                             setName();
                                            });
                                            
         $('#destino').change(function(data){
                                             setName();
                                            });

         });
                                  
      	function valida(valor, id) {
		   if(valor.indexOf("_") == -1){
		      var hora = valor.split(":")[0];
		      if(parseInt(hora) > 23 ){
		           $("#"+id).val("");
		      }
		   }
        }
        
        function setName(){
                 var st = $('input:radio[name=srvvacio]:checked').val();
                 if (st == 1){
                    var or = $('#origen option:selected').text();
                    var de = $('#destino option:selected').text();
                    var cor = $('#origen').val();
                    var cde = $('#destino').val();
                    if ((cor == 1)||(cor == 3)){
                       or = 'CENTRAL';
                    }
                    if ((cde == 1)||(cde == 3)){
                       de = 'CENTRAL';
                    }
                    $('#crono').val("VACIO ("+or+" - "+de+")");
                 }
        }
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend {padding: 0.5em;}
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
.button-small{font-size: 62.5%;}
div#users-contain {margin: 20px 0; font-size: 62.5%;}
div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
//input.text { margin-bottom:12px; width:95%; padding: .4em; }
#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}

#upnwserv .error{
	font-size:0.8em;
	color:#ff0000;
}

fieldset .div {
  padding-top: 7px;
  padding-right: 7px;
  padding-bottom: 7px;
  padding-left: 7px;
}

form table td{
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  padding-left: 3px;
}

</style>
<BODY>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="upnwserv">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Definir Servicio</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%" name="table">
                                <tr>
                                    <td WIDTH="20%"><label for="crono">Nombre</label></td>
                                    <td><input id="crono" name="crono" class="ui-widget ui-widget-content  ui-corner-all {required:true}" size="60" minlength="2"/><span></span></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="crono">Vacio</label></td>
                                    <td colspan="2">Si<input name="srvvacio" type="radio" value="1" id="okemp">No<input id="nookemp" name="srvvacio" checked type="radio" value="0"></td>

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
                                <tr id="cliempty">
                                    <td WIDTH="20%">Afectar vacio a:</td>
                                    <td><select id="cliente_vacio" name="cliente_vacio" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
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
                                    <td><select id="origen" name="origen"  class="ui-widget-content  ui-corner-all"  validate="required:true">
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
                                    <td><select id="destino" name="destino"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

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
                                    <td WIDTH="20%">Km Recorrido</td>
                                    <td colspan="2"><input id="km" maxlength="4" size="4" name="km" class="{required:true} number ui-widget ui-widget-content  ui-corner-all" /><span></span></td>
                                </tr>
                                <tr>
                                <td WIDTH="20%">Duracion Servicio</td>
                                    <td colspan="2"><input id="tiempo" maxlength="5" size="5" name="tiempo" class="{required:true} hora ui-widget ui-widget-content  ui-corner-all" /><span></span></td>
                                </tr>
                                <tr>
                                    <td colspan="4"><hr align="tr">Horarios del servicio</td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                         <div id="users-contain" class="ui-widget">

	                                          <table id="contacts" class="ui-widget ui-widget-content">
		                                             <thead>
			                                                <tr class="ui-widget-header ">
				                                                <th>H. Citacion</th>
				                                                <th>H. Salida</th>
				                                                <th>H. Llegada</th>
				                                                <th>H. Fin Servicio</th>
				                                                <th>Turno</th>
				                                                <th>Tipo Servicio</th>
				                                                <th>Ida Vuelta</th>
			                                                 </tr>
	                                                 </thead>
		                                             <tbody>
		                                             </tbody>
                                              </table>
                                              <input type="button" class="ui-widget ui-icon-plusthick" id="add-srv" value="Agregar Horarios al Servicio" align="right"/>


                                         </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input id="savesrv" class="boton" type="submit" value="Guardar Servicio" name="envioFormulario"></td>
                                </tr>
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="ssv"/>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

         <div id="dialog-form" title="Agregar Horarios al Servicio">
              <form id="upcontact">
	                <fieldset>
                              <div class="div">
		                      <label for="h_cita">Hora Citacion</label>
		                      <input type="text" name="h_cita" id="h_cita" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
                              <label for="h_salida">Hora Salida</label>
		                      <input type="text" name="h_salida" id="h_salida" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="h_llegada">Hora Llegada</label>
		                      <input type="text" name="h_llegada" id="h_llegada" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="h_fin">Hora Fin Servicio</label>
		                      <input type="text" name="h_fin" id="h_fin" maxlength="5" size="5" class="hora ui-widget-content ui-corner-all {required:true}" onblur="valida(this.value, this.id);"/>
                              <span></span>
                              </div>
                              <div class="div">
		                      <label for="turno">Turno Servicio</label>
		                      <span></span>
                              <?php selectTurnos(0,0); ?>
                              </div>
                              <div class="div">
		                      <label for="tipo">Tipo Servicio</label>
		                      <span></span>
                              <?php selectTipo(0,0); ?>
                              </div>
                              <div class="div">
		                      <label for="h_fin">Ida/Vuelta</label>
		                      <span></span>
                              <?php selectIV(0,0);?>
                              </div>
                    </fieldset>
           			<fieldset id="botonera" style="border:none; text-align: right;">
				              <input id="envioFormulario" class="boton" type="submit" value="Agregar Horario" name="envioFormulario">
                    </fieldset>
              </form>
         </div>
</BODY>
</HTML>
