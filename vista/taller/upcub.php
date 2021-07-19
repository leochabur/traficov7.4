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

        $('#fechac').datepicker();
        $(':button, :submit').button();


		$("#nuevoM").click(function(){
		                             $('#nuevoM').toggle();
		                             $('#guardarM').toggle();
		                             $('#apenomM').toggle();
		});
		$("#saveM").click(function(){
                                     $.post('/modelo/taller/upcub.php', {marca:$('#ape').val(), accion: 'med'}, function(data){$('#selMar').append(data);$('#selMar').selectmenu({width: 400});});
                                     $('#ape').val('');
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
                                     $.post('/modelo/taller/upcub.php', {nombre:$('#espec').val(), accion: 'medida'}, function(data){$('#medida').append(data);$('#medida').selectmenu({width: 400});});
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
                                     $.post('/modelo/taller/upcub.php', {nombre:$('#diagnostic').val(), accion: 'deposito'}, function(data){$('#depositoC').append(data);$('#depositoC').selectmenu({width: 400});});
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
                                                        $.post('/modelo/taller/upcub.php', datos, function(data){
                                                                                                                 var obj = jQuery.parseJSON(data);
                                                                                                                 if (obj == '-1'){
                                                                                                                    alert('Codigo de cubierta existente en la Base de Datos');
                                                                                                                 }
                                                                                                                 else{
                                                                                                                      $('#codigo, #fechac').val('');
                                                                                                                 }

                                                                                                                 });
                                                        
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
                   <legend class="ui-widget ui-widget-header ui-corner-all">Ingresar Cubierta</legend>
                         <table id="tablita">
                                <tr>
                                    <td>Marca</td>
                                    <td>     <div id="marca">
                                                  <select size="1" id="selMar" name="selMar">
                                                     <?php armarSelect('marcaCubierta', "marca", 'id', "upper(marca)", "");?>
                                                  </select>
                                             </div>
                                    </td>
                                    <td>
                                        <input type="button" value="Nuevo" id="nuevoM">
                                    </td>
                                    <td>
                                        <div id="apenomM" style="display:none">
                                             Marca<input type="text" size="20" id="ape">
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
                                    <td>Medida</td>
                                    <td>
                                        <div id="especialidad" name="especialidad">
                                             <select size="1" id="medida" name="medida">
                                                 <?php armarSelect('medidasCubiertas', "medida", 'id', "upper(medida)", "");?>
                                             </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="button" value="Nuevo" id="nuevaE">
                                    </td>
                                    <td>
                                        <div id="espTxt" style="display:none">
                                             Medida<input type="text" size="20" id="espec">
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
                                    <td>Estado</td>
                                    <td>
                                        <div id="centroAs">
                                             <select size="1" id="estadoC" name="estadoC">
                                                     <?php armarSelect('estadoCubiertas', "estado", 'id', "upper(estado)", ""); ?>
                                             </select>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="button" value="Nuevo" id="nuevoC">
                                    </td>
                                    <td>
                                        <div id="ctroTxt" style="display:none">
                                             Estado<input type="text" size="20" id="ctroAsis">
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
                                        <td>Deposito</td>
                                        <td>

                                            <div id="diagn">
                                                 <select size="1" id="depositoC" name="depositoC">
                                                 <option value="0"></option>
                                                         <?php armarSelect('depositos', "nombre", 'id', "upper(nombre)", ""); ?>
                                                 </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="button" value="Nuevo" id="nuevoD">
                                        </td>
                                        <td>
                                            <div id="diagTxt" style="display:none">
                                                 Deposito<input type="text" size="20" id="diagnostic">
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
                                        <td>Codigo</td>
                                        <td colspan="5">
                                            <div>
                                                 <input id="codigo" name="codigo" type="text" size="15" class="required ui-widget ui-widget-content  ui-corner-all">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            Fecha Compra
                                        </td>
                                        <td colspan="5">
                                            <div>
                                                 <input id="fechac" name="fechac" type="text" size="15" class="ui-widget ui-widget-content  ui-corner-all">
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
            <input type="hidden" name="accion" value="svecub">
         </form>
</body>
<?php
          print "<script type='text/javascript'>
                    $(\"#empleador> option[value=$opcion]\").attr('selected', 'selected');
                    $.post('/modelo/rrhh/upcertmed.php', {accion:'loademp', emplor:\$('#empleador').val()}, function(data){\$('#selCond').html(data);$('select').selectmenu({width: 400});});
                </script>";
?>
</html>

