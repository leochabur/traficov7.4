<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '/nuevotrafico');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
     $opcion = getOpcion('sel-combo-def', $_SESSION['structure']);
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

table tr td{padding: 3px;}
#upcm .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
	$(function() {

        $('#desde, #hasta').datepicker();
        $(':button, :submit').button();
        $("#empleador").change(function(){
                                $.post('/modelo/rrhh/upcertmed.php', {accion:'loademp', emplor:$('#empleador').val()}, function(data){
                                                                                                                                      $('#selCond').html(data);
                                                                                                                                      $('select').selectmenu({width: 400});
                                                                                                                                      });

        });
        $("#selCond").change(function() {
                                       $('#novedades').load('/modelo/rrhh/upcertmed.php', {nombre:$(this).val(), accion: 'loadNov'}, function(){$('#selNov').selectmenu({width: 400});});
        });

		$("#nuevoM").click(function(){
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});
		$("#saveM").click(function(){
                                     $.post('/modelo/rrhh/upcertmed.php', {apellido:$('#ape').val(), nombre:$('#nom').val(), accion: 'med'}, function(data){$('#selMed').append(data);$('#selMed').selectmenu({width: 400});});
                                     $('#ape').val('');
                                     $('#nom').val('');
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});
		$("#cancelM").click(function(){
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});

		$("#nuevaE").click(function(){
		                             $('#nuevaE').toggle();
		                             $('#guardarE').toggle();
		                             $('#espTxt').toggle();
		});
		$("#saveE").click(function(){
                                     $.post('/modelo/rrhh/upcertmed.php', {nombre:$('#espec').val(), accion: 'espec'}, function(data){$('#selEsp').append(data);$('#selEsp').selectmenu({width: 400});});
                                     $('#espe').val('');
		                             $('#espTxt').toggle();
		                             $('#guardarE').toggle();
		                             $('#nuevaE').toggle();
		});
		$("#cancelE").click(function(){
		                             $('#nuevaE').toggle();
		                             $('#guardarE').toggle();
		                             $('#espTxt').toggle();
		});

		$("#nuevoC").click(function(){
		                             $('#nuevoC').toggle();
		                             $('#guardarC').toggle();
		                             $('#ctroTxt').toggle();
		});
		$("#saveC").click(function(){
                                     $.post('/modelo/rrhh/upcertmed.php', {nombre:$('#ctroAsis').val(), accion: 'ctroAsis'}, function(data){$('#selCtro').append(data);$('#selCtro').selectmenu({width: 400});});
                                     $('#ctroAsis').val('');
		                             $('#ctroTxt').toggle();
		                             $('#guardarC').toggle();
		                             $('#nuevoC').toggle();
		});
		$("#cancelC").click(function(){
		                             $('#nuevoC').toggle();
		                             $('#guardarC').toggle();
		                             $('#ctroTxt').toggle();
		});

		$("#nuevoD").click(function(){
		                             $('#nuevoD').toggle();
		                             $('#guardarD').toggle();
		                             $('#diagTxt').toggle();
		});
		$("#saveD").click(function(){
                                     $.post('/modelo/rrhh/upcertmed.php', {nombre:$('#diagnostic').val(), accion: 'diagnostic'}, function(data){$('#selDiag').append(data);$('#selDiag').selectmenu({width: 400});});
                                     $('#diagnostic').val('');
		                             $('#diagTxt').toggle();
		                             $('#guardarD').toggle();
		                             $('#nuevoD').toggle();
		});
		$("#cancelD").click(function(){
		                             $('#nuevoD').toggle();
		                             $('#guardarD').toggle();
		                             $('#diagTxt').toggle();
		});
		
		$("#upcm").validate({
                             submitHandler: function(e){
                                                        var datos = $('#upcm').serialize();
                                                        $.post('/modelo/rrhh/upcertmed.php', datos, function(data){alert(data);});
                                                        }
                             });
	});
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="upcm" name="upcm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
                   <legend class="ui-widget ui-widget-header ui-corner-all">Ingresar Certificado Medico</legend>
                         <table id="tablita">
                                <tr>
                                    <td>Medico</td>
                                    <td>     <div id="medicos">
                                                  <select size="1" id="selMed" name="selMed">
                                                     <?php armarSelect('medicos', "apellido", 'id', "concat(apellido,', ',nombre)", "");?>
                                                  </select>
                                             </div>
                                    </td>
                                    <td>
                                        <input type="button" value="Nuevo" id="nuevoM">
                                    </td>
                                    <td>
                                        <div id="apenomM" style="display:none">
                                             Apellido<input type="text" size="20" id="ape">
                                             Nombre<input type="text" size="20" id="nom">
                                        </div>
                                    </td>
                                    <td>
                                        <div id="guardarM" style="display:none">
                                             <input type="button" value="Guardar" id="saveM">
                                             <input type="button" value="Cancelar" id="cancelM">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Especialidad</td>
                                    <td>
                                        <div id="especialidad" name="especialidad">
                                             <select size="1" id="selEsp" name="selEsp">
                                                 <?php armarSelect('especialidades', "especialidad", 'id', "especialidad", "");?>
                                             </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="button" value="Nuevo" id="nuevaE">
                                    </td>
                                    <td>
                                        <div id="espTxt" style="display:none">
                                             Especialidad<input type="text" size="20" id="espec">
                                        </div>
                                    </td>
                                    <td>
                                        <div id="guardarE" style="display:none">
                                             <input type="button" value="Guardar" id="saveE">
                                             <input type="button" value="Cancelar" id="cancelE">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Ctro. Asistencial</td>
                                    <td>
                                        <div id="centroAs">
                                             <select size="1" id="selCtro" name="selCtro">
                                                     <?php armarSelect('ctrosasistenciales', "nombre", 'id', "nombre", ""); ?>
                                             </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="button" value="Nuevo" id="nuevoC">
                                    </td>
                                    <td>
                                        <div id="ctroTxt" style="display:none">
                                             Ctro Asistencial<input type="text" size="20" id="ctroAsis">
                                        </div>
                                    </td>
                                    <td>
                                        <div id="guardarC" style="display:none">
                                             <input type="button" value="Guardar" id="saveC">
                                             <input type="button" value="Cancelar" id="cancelC">
                                        </div>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td>Diagnostico</td>
                                        <td>
                                            <div id="diagn">
                                                 <select size="1" id="selDiag" name="selDiag">
                                                         <?php armarSelect('diagnosticos', "diagnostico", 'id', "diagnostico", ""); ?>
                                                 </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="button" value="Nuevo" id="nuevoD">
                                        </td>
                                        <td>
                                            <div id="diagTxt" style="display:none">
                                                 Diagnostico<input type="text" size="20" id="diagnostic">
                                            </div>
                                        </td>
                                        <td>
                                            <div id="guardarD" style="display:none">
                                                 <input type="button" value="Guardar" id="saveD">
                                                 <input type="button" value="Cancelar" id="cancelD">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    <td colspan="5">
                                        <hr color="#202020">
                                    </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Empleadores
                                        </td>
                                        <td colspan="5">
                                            <div id="conduct">
                                            <select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                 armarSelect('empleadores', 'razon_social', 'id', 'razon_social', "(id_estructura = ".STRUCTURED.") and (activo)");
                                                ?>
                                        </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Empleado
                                        </td>
                                        <td colspan="5">
                                            <div id="conduct">
                                                 <select size="1" name="selCond" id="selCond">
                                                 </select>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Novedades
                                        </td>
                                        <td>
                                            <div id="novedades">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Fecha Certificado
                                        </td>
                                        <td colspan="5">
                                            <div>
                                                 <input id="desde" name="desde" type="text" size="15" class="required ui-widget ui-widget-content  ui-corner-all">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Vigencia Hasta
                                        </td>
                                        <td colspan="5">
                                            <div>
                                                 <input id="hasta" name="hasta" type="text" size="15" class="required ui-widget ui-widget-content  ui-corner-all">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                    <tr>
                                        <td>
                                            Observaciones
                                        </td>
                                        <td colspan="5">
                                            <div>
                                                 <textarea rows="4" cols="20" name="obs" class="ui-widget ui-widget-content  ui-corner-all"></textarea>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div id='resu'></div>
                                        </td>
                                        <td colspan="5">
                                            <div align="right"><input type="submit" value="Guardar" name="enviar"></div>
                                        </td>
                                    </tr>
                         </table>
            </fieldset>
            <input type="hidden" name="accion" value="svecert">
         </form>
</body>
<?php
          print "<script type='text/javascript'>
                    $(\"#empleador> option[value=$opcion]\").attr('selected', 'selected');
                    $.post('/modelo/rrhh/upcertmed.php', {accion:'loademp', emplor:\$('#empleador').val()}, function(data){\$('#selCond').html(data);$('select').selectmenu({width: 400});});
                </script>";
?>
</html>

