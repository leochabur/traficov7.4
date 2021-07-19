<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');     */
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');

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
        $('#savesrv').button();
        $('#cliente, #interno').selectmenu({width: 350});
        $('#conductor').selectmenu({width: 450});
        <?php
             if ($cantTripulacion > 2){
                print "$('#conductor2').selectmenu({width: 450});
                      $('#conductor3').selectmenu({width: 450});";
             }
        ?>
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("button").button({icons: {
                                       primary: "ui-icon-disk"
                                       }});
		$("#fservicio").datepicker({
                                    dateFormat : 'dd/mm/yy',
                                    autoOpen: true,
                                    onSelect: function(date) {
                                                                  $.post("cargar_combo_conductores.php", {fecha: date}, function(data){

                                                                                                                                    $("#conductor").empty();
                                                                                                                                    $("#conductor").append(data);
                                                                                                                                    $('#conductor').selectmenu({width: 450});
                                                                                                                                    <?php
                                                                                                                                         if ($cantTripulacion > 2){
                                                                                                                                            print'$("#conductor2").empty();
                                                                                                                                            $("#conductor2").append(data);
                                                                                                                                            $("#conductor2").selectmenu({width: 450});
                                                                                                                                            $("#conductor3").empty();
                                                                                                                                            $("#conductor3").append(data);
                                                                                                                                            $("#conductor3").selectmenu({width: 450});';
                                                                                                                                         }
                                                                                                                                    
                                                                                                                                    ?>
                                                                                                                                    });
                                                                 }
                                    });

		$.mask.definitions['~']='[012]';
        $.mask.definitions['%']='[012345]';
        <?php
             if ($cantTripulacion <= 2){
                $horarios=5;
                print '$(".hora").mask("~9:%9",{completed:function(){}});';
             }
             else{
                  $horarios=16;
                  print '$(".hora").mask("99/99/9999 ~9:%9",{completed:function(){}});';
             }
        ?>

        
        $("#cliente").change(function(){
                                         var cliente = $("#cliente option:selected").val();
                                         $.post("/vista/paneles/comboDB.php",{cli: cliente, accion: 'lcr'},function(data){$("#serv-cli").html(data);
                                                                                                                      $('#servicios').selectmenu({width: 450});
                                                                                                                     })



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

                                                                                                                                                    $('#commentForm').each (function(){
                                                                                                                                                                                      this.reset();
                                                                                                                                                                                      });
                                                                                                                                                    $("#serv-cli").html('');
                                                                                                                                                    $("#hora-servi").html('');
                                                                                                                                                    $("#cliente option[value=0]").attr("selected", "selected");
                                                                                                                                                    $("#conductor option[value=249]").attr("selected", "selected");
                                                                                                                                                    $('select').selectmenu({width: 350});
                                                                                                                                                    });
                                                              }
                                   });
      $("#fservicio").focus();
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
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  padding-left: 3px;
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
         <form class="cmxform" id="commentForm" method="get" action="">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Crear Orden de Servicio</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Servicio</label></td>
                                    <td><input id="fservicio" name="fservicio" class="{required:true} ui-widget ui-widget-content  ui-corner-all" minlength="2"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td>
                                        <select id="cliente" name="cliente" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">Seleccione un Cliente</option>
                                                <?php
                                                  armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure]) and (activo)");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Servicios</td>
                                    <td>
                                        <div id="serv-cli">

                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Horarios</td>
                                    <td>
                                         <div id="hora-servi">

                                        </div>
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

                                    </td>
                                </tr>
                                <?php
                                if ($cantTripulacion > 2){
                               print' <tr>
                                    <td WIDTH="20%">Conductor 2</td>
                                    <td><select id="conductor2" name="conductor2" class="ui-widget-content  ui-corner-all">
                                                <option value="0"></option>

                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                                                <tr>
                                    <td WIDTH="20%">Conductor 3</td>
                                    <td><select id="conductor3" name="conductor3" class="ui-widget-content  ui-corner-all">
                                                <option value="0"></option>

                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr> ';
                                }
                                ?>
                                <tr>
                                    <td WIDTH="20%">Interno</td>
                                    <td><select id="interno" name="interno" class="ui-widget-content  ui-corner-all">
                                                <option value="0"></option>
                                                <?php
                                                     armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "(id_estructura = ".$_SESSION['structure'].") and (activo)");
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Citacion</td>
                                    <td colspan="2"><input id="hcitacion" maxlength="<?php echo $horarios?>" size="<?php echo $horarios?>" name="hcitacion" class="required hora ui-widget ui-widget-content  ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Salida</td>
                                    <td colspan="2"><input id="hsalida" maxlength="<?php echo $horarios?>" size="<?php echo $horarios?>" name="hsalida" class="required hora ui-widget ui-widget-content  ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Llegada a Planta</td>
                                    <td colspan="2"><input id="hllegada" maxlength="<?php echo $horarios?>" size="<?php echo $horarios?>" name="hllegada" class="required hora ui-widget ui-widget-content  ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Hora Finalizacion Servicio</td>
                                    <td colspan="2"><input id="hfinserv" maxlength="<?php echo $horarios?>" size="<?php echo $horarios?>" name="hfinserv" class="required hora ui-widget ui-widget-content  ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Km del Servicio</td>
                                    <td colspan="2"><span></span><input id="km" name="km" size="5" class="required ui-widget-content number  ui-corner-all" /></td>
                                </tr>
                                <tr>
                                    <td colspan="3" align="right"><input id="savesrv" class="boton" type="submit" value="Guardar Servicio" name="envioFormulario"></td>
                                </tr>
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="soser"/>
	<input type="hidden" name="cantTr" id="cantTr" value="<?php echo $cantTripulacion;?>"/>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

</BODY>
</HTML>
