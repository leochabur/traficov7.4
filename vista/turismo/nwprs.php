<?php
      //  die('okkkkkkkkkkkkkkkkkk');
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
     set_time_limit(0);
     session_start();
     //error_reporting(0);

   /*  include('../../modelo/provincia.php');*/       

     include('../../modelo/utils/dateutils.php');
     include('../paneles/viewpanel.php');
     include('../main.php');
     include_once('../../modelsORM/controller.php');

     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
     $edit = false;
     if (isset($_GET['psn'])){
        $numero = $_GET['psn'];
        $edit = true;
        $pres = find('Presupuesto', $numero);
     }
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script>
    var form, dialog;
    var viajes = new Array();


	$(function() {
	    $('#precioNeto').keyup(function() {
                                            updateValues();
                                            });
		  dialog = $( "#dialog-form" ).dialog({
			autoOpen: false,
			height: 400,
			width: 750,
			modal: true,
			buttons: {
				"Cancelar": function() {
                                       form[ 0 ].reset();
                                       form.removeClass('error');
					                   dialog.dialog( "close" );
				                    },
                "Agregar viaje": function(){
                                          form.submit();
                                          }
			},
			close: function() {
				form[ 0 ].reset();
				form.removeClass( "ui-state-error" );
			}
		});


		form = dialog.find( "form" );

        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
        $('.fecha').datepicker({dateFormat:'dd/mm/yy', autoOpen:false});
		$("#addvje").button().click(function(){
                                               dialog.dialog('open');
                                               });
        $('#envioFormulario').button();


		$.mask.definitions['~']='[012]';
    $.mask.definitions['%']='[012345]';
    $(".hora").mask("~9:%9");

    <?php if ($edit) print "$(\"#canal option[value='".$pres->getCanalPedido()->getId()."']\").attr('selected', 'selected');"; ?>

        $('.select1').selectmenu({width: 250});
        $('.clase').selectmenu({width: 350});
        $('.clase2').selectmenu({width: 150});
        $('#cliente').change(function(){
                                        $.post('/modelo/turismo/nwprs.php',
                                                {accion:'cccli', cli: $(this).val()},
                                                function (res){
                                                                $('#cuil_cuit').html(res);
                                                });
                                        
        });
        $('#pagoanti').change(function(){
                                          if ($(this).is(':checked')){
                                            $('#flimite').addClass( "required" );
                                          }
                                          else{
                                            $('#flimite').removeClass( "required" );
                                          }
        });
        $('#addprs').validate({
                                  submitHandler: function(){
                                                            $('#envioFormulario').hide();
                                                            var dataForm = $('#addprs');
                                                            var datos = dataForm.serialize();
                                                            <?php 
                                                                if ($edit)
                                                                  print "var vjes = JSON.stringify(viajes);";
                                                                else
                                                                  print "var vjes = viajes.join(',');";
                                                            ?>
                                                            datos+='&accion=<?php print $edit?'editps':'addps'; ?>&vjes='+vjes;
                                                            
                                                            $.post("/modelo/turismo/nwprs.php",
                                                                   datos,
                                                                   function(response){ 
                                                                                                                                           
                                                                      var res = $.parseJSON(response);
                                                                      if (res.status){
                                                                          $('#envioFormulario').show();
                                                                          dataForm[ 0 ].reset();
                                                                          resetViajes();
                                                                      }
                                                                      else{
                                                                        alert(res.message);
                                                                      }
                                                                      $('#envioFormulario').show();

                                                                   });                                                  

                                                           }
                                  });
	});
	
  function resetViajes(){
      viajes.forEach(function(element) {
                                    $('#tr'+element).remove();
                                  });
      viajes.length = 0;
  }

  function updateValues(){
      var neto = $('#precioNeto').val();
      var iva = (neto*0.105);
      var final = (neto *1.105);
      $('#iva').val(iva);
      $('#preciofinal').val(final);
  }

	</script>

<style type="text/css">
body { font-size: 82.5%; }

.top {
    padding-top: 25px;
}
.error{
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
         <form class="cmxform" id="addprs" name="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Cargar nuevo presupuesto</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="75%">
                                <tr>
                                    <td WIDTH="20%">Numero</td>
                                    <td>

                                        <?php
                                              $dis="";
                                              if ($edit)
                                              {
                                                $num = $pres->getId(); 
                                                $dis = 'disabled';
                                              }
                                              else{
                                                $next = call('Presupuesto', 'proximoNumeroPresupuesto');
                                                $num = $next[0][1]+1; 
                                              }                                             
                                              print "<label $dis>".('0001-'.str_pad($num, 6,'0', STR_PAD_LEFT))."</label>";                                              
                                        ?>
                                    </td>
                                    <td>
                                    </td>
                                </tr>                          
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td><select id="cliente" name="cliente" title="Please select something!"  class="ui-widget-content  ui-corner-all clase"  <?php print $edit?'':''; ?>>
                                                <?php
                                                     if ($edit)
                                                        print "<option value='".$pres->getCliente()->getId()."'>".$pres->getCliente()."</option>";
                                                     else{
                                                        print '<option value="0">Seleccione un Cliente</option>';
                                                        print clientesOptions();
                                                     }
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                  <td>CUIT/CUIL</td>
                                  <td id="cuil_cuit"><?php print ($edit?$pres->getCliente()->getCuit():""); ?></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Canal de pedido</td>
                                    <td><select id="canal" name="canal"  class="ui-widget-content  ui-corner-all clase2"  validate="required:true">
                                                <?php
                                                     print canalesPedidosOptions();
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Pedido</label></td>
                                    <td><input id="fpedido" name="fpedido" class="ui-widget ui-widget-content  ui-corner-all required fecha" minlength="2" 
                                      <?php print ($edit?"value='".$pres->getFechaSolicitud()->format('d/m/Y')."'":''); ?> /></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Confeccion</label></td>
                                    <td><input id="fconf" name="fconf" class="ui-widget ui-widget-content  ui-corner-all fecha" minlength="2"
                                      <?php
                                          if ($edit && ($pres->getFechaConfeccion())) 
                                            print ($edit?("value='".$pres->getFechaConfeccion()->format('d/m/Y')."'"):''); 
                                        ?> 
                                      /></td>
                                    <td></td>
                                </tr>                                
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Respuesta</label></td>
                                    <td><input id="fresp" name="fresp" class="ui-widget ui-widget-content  ui-corner-all fecha" minlength="2"
                                        <?php
                                          if ($edit && ($pres->getFechaInforme())) 
                                            print $edit?"value='".$pres->getFechaInforme()->format('d/m/Y')."'":''; 
                                        ?> 
                                      /></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Total Pax</td>
                                    <td colspan="2"><input id="pax" size="4" name="pax" class="<?php echo $required;?> ui-widget-content number  ui-corner-all required" 
                                      <?php print $edit?"value='".$pres->getPax()."'":''; ?>/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Precio Neto</td>
                                    <td colspan="2"><input id="precioNeto" size="6" name="precioNeto" class="ui-widget-content ui-corner-all"
                                      <?php print $edit?"value='".$pres->getMontoSIva()."'":''; ?>/>
                                    </td>
                                </tr> 
                                <tr>
                                    <td WIDTH="20%">IVA</td>
                                    <td colspan="2"><input id="iva" size="6" name="iva" class="ui-widget-content ui-corner-all" readonly
                                      <?php print $edit?"value='".$pres->getIva()."'":''; ?>/>
                                    </td>
                                </tr>                                                                  

                                <tr>
                                    <td WIDTH="20%">Precio Final (IVA Incluido)</td>
                                    <td colspan="2"><input id="preciofinal" size="6" name="preciofinal" class="ui-widget-content ui-corner-all" readonly
                                      <?php print $edit?"value='".$pres->getMontoFinal()."'":''; ?>/>
                                      <input name="efc" id="efc" type="checkbox" <?php echo ($edit&&$pres->getEmiteComprobante())?"checked":""; ?>></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Pago Anticipado Obligatorio</td>
                                    <td colspan="2"><input type="checkbox" name="pagoanti" id="pagoanti" class="ui-widget-content ui-corner-all"
                                      <?php echo ($edit&&$pres->getPagoAnticipado())?"checked":""; ?>></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Limite Pago</label></td>
                                    <td><input id="flimite" name="flimite" class="ui-widget ui-widget-content  ui-corner-all fecha" minlength="2"
                                        <?php
                                          if ($edit && ($pres->getFechaInforme())) 
                                            print $edit?"value='".$pres->getFechaInforme()->format('d/m/Y')."'":''; 
                                        ?> 
                                      /></td>
                                    <td></td>
                                </tr>                                

                                <tr>
                                    <td WIDTH="20%">Requiere Orden Compra</td>
                                    <td colspan="2"><input type="checkbox" name="requiereOC" class="ui-widget-content ui-corner-all"
                                      <?php echo ($edit&&$pres->getConfConOrdenCompra())?"checked":""; ?>></td>
                                </tr>      
                                <tr>
                                  <td>Gastos a Cargo Empresa</td>
                                  <td>
                                    <?php
                                      $gastos = gastosPresupuestos();
                                      foreach ($gastos as $gasto) {
                                        $checked = "";
                                        
                                        if (($edit) && ($pres->existeGasto($gasto)))
                                          $checked = "checked";
                                        print "$gasto<input type='checkbox' class='gtos' name='gas-".$gasto->getId()."' $checked/>";
                                      }
                                    ?>

                                  </td>
                                </tr>

                                <tr>
                                    <td>Observaciones</td>
                                    <td><textarea rows="5" cols="45" class="ui-widget-content ui-corner-all" name="observa"><?php print $edit?$pres->getObservaciones():'';?></textarea></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Datos de contacto</td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Nombre</td>
                                    <td colspan="2"><input id="nomcontacto" name="nomcontacto" class="ui-widget-content ui-corner-all" 
                                      <?php print $edit?"value='".$pres->getNombreContacto()."'":'';?>/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Telefono</td>
                                    <td colspan="2"><input id="telcontacto" name="telcontacto" class="ui-widget-content ui-corner-all" 
                                      <?php print $edit?"value='".$pres->getTelefonoContacto()."'":'';?>/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">E-mail</td>
                                    <td colspan="2"><input id="mailcontacto" name="mailcontacto" class="ui-widget-content ui-corner-all" 
                                      <?php print $edit?"value='".$pres->getMailContacto()."'":'';?>/></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="ui-widget ui-widget-header ui-corner-all">Agregar viajes al presupuesto</td>
                                </tr>
                                <tr>
                                    <td colspan="2" id="tdvje">
                                    <?php
                                      if (!$edit){
                                    ?> 
                                        <table width="100%" id="vjelst"  class="table table-zebra">
                                               <thead>
                                                      <tr>
                                                      <th>Fecha Salida</th>
                                                      <th>Origen</th>
                                                      <th>Destino</th>
                                                      <th>H. Salida</th>
                                                      <th>Pax</th>
                                                      <th>Accion</th>
                                                      </tr>
                                               </thead>
                                               <tbody>
                                               </tbody>
                                        </table>
                                        
                                      <?php
                                        }
                                        else{
                                          
                                          $elem = "";
                                          foreach ($pres->getViajes() as $viaje) {
                                            if ($elem)
                                              $elem.=", ".$viaje->getId().":".($viaje->getEliminado()?1:0);
                                            else
                                              $elem = $viaje->getId().":".($viaje->getEliminado()?1:0);
                                          }
                                          print '<script>viajes = {'.$elem.'}; </script>';
                                      ?>
                                          <script type="text/javascript">
                                            $.post('/modelo/turismo/nwprs.php',
                                                  {accion:'loadSrvPr', id:<?php print $pres->getId()?>},
                                                  function(response){                                            
                                                    $('#tdvje').html(response);
                                                  });
                                          </script>

                                      <?php
                                        }
                                      ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div><input type="button" value="Agregar Viaje" id="addvje"></div>
                                    </td>
                                </tr>
                                <tr>
                                <?php
                                     if (!isset($_GET['otmo'])){
                                ?>
                                    <td colspan="3" align="right"><input id="envioFormulario" class="boton" type="submit" value="Guardar Presupuesto" name="envioFormulario"></td>
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

  <input type="hidden" name="idprs" value="<?php print $numero; ?>" />
</form>
</div>

<?php
     include ('formaddvje.php');
?>

         

</BODY>
</HTML>
