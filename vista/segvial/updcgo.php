<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');*/
     include('../paneles/viewpanel.php');
     include('../main.php');
     //define('RAIZ', '');
     define('STRUCTURED', $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     if(isset($_GET['nro'])){
          $edit=1;
          $sql = "SELECT id_siniestro, resolucion, id_solicitante, nro_descargo, id_empleado, date_format(fecha_emision, '%d/%m/%Y') as emision, date_format(fecha_entrega, '%d/%m/%Y') as entrega, date_format(fecha_respuesta, '%d/%m/%Y') as respuesta, descripcion_hecho, detalle_resolucion, mediante
                  FROM descargos
                  WHERE id = $_GET[nro]";
          $result = ejecutarSQL($sql);
          $row = mysql_fetch_array($result);
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
          <?php
          echo $edit?"$('#destinatario option[value=$row[id_empleado]]').attr('selected','selected');":"";
          echo $edit?"$('#solicitante option[value=$row[id_solicitante]]').attr('selected','selected');":"";
          echo $edit?"$('#resolucion option[value=$row[resolucion]]').attr('selected','selected');":"";

          ?>


        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
		$("input:button").button({icons: {
                                       primary: "ui-icon-disk"
                                       }});
        $('#envioFormulario').button();
        $(':button').button();
        $(':submit').button();
		$("#emision, #entrega, #rpta").datepicker({
                                    dateFormat : 'dd/mm/yy'
                                   });
		$.mask.definitions['~']='[012]';
        $.mask.definitions['%']='[012345]';
        $(".hora").mask("~9:%9",{completed:function(){}});
        $('#km, #n_interno').mask("9999");
        $('#destinatario, #solicitante, #resolucion').selectmenu({width: 350});
        $.post("/modelo/segvial/updcgo.php", {accion: 'sincnd', cnd:$("#destinatario").val()}, function(data){$('#siniestro').html(data); <?php echo $edit?"$('#siniestro option[value=$row[id_siniestro]]').attr('selected','selected');":"";?>$("#siniestro").selectmenu({width: 350});});


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

        $('#commentForm').validate({
                                  submitHandler: function(){
                                                            var datos = $("#commentForm").serialize();
                                                            $.post("/modelo/segvial/updcgo.php", datos, function(data) {
                                                                                                                         var response = $.parseJSON(data);
                                                                                                                         if (response.status){
                                                                                                                            alert(response.msge);
                                                                                                                            <?php if($edit==1){?>
                                                                                                                                  $(location).attr("href","/vista/segvial/readcgo.php?ds=<?php print$_GET[ds];?>&hs=<?php print $_GET[hs];?>&cn=<?php print $_GET[cnd]?>");

                                                                                                                            <?php
                                                                                                                                 }else{?>
                                                                                                                            $('#siniestro').html('<option value=\'0\'>NO DETALLA</option>');
                                                                                                                            $('#siniestro').selectmenu({width: 450});
                                                                                                                            $('#commentForm')[0].reset();
                                                                                                                            $.post("/modelo/segvial/updcgo.php", {accion: 'proxnrodcgo'}, function(data){$('#ndesc').val(data);});
                                                                                                                            <?php }?>
                                                                                                                         }
                                                                                                                         else{
                                                                                                                                  alert(response.sql);
                                                                                                                                  alert(response.msge);
                                                                                                                         }
                                                                                                                         });
                                                           }
                                  });
        <?php
             if ($edit){
        ?>
                $("#print").click(function(){
                                             var nro = <?php echo $row['nro_descargo']; ?>;
                                             var acc = 'res';
                                             var detalle = $("#detalleresolucion").val();
                                             var reso = $("#resolucion").val();
                                             var fecha = $("#rpta").val();
                                             alert(detalle);
                                             $.post("/modelo/segvial/updcgo.php", {num:nro, accion:acc, det:detalle, res:reso, fec:fecha}, function(data) {
                                                                                                                                                           var response = $.parseJSON(data);
                                                                                                                                                           if (response.status){
                                                                                                                                                              window.open("/modelo/segvial/print_reso.php?dcgo=<?php echo $row['nro_descargo']?>", '_blank');
                                                                                                                                                           }
                                                                                                                                                           else{
                                                                                                                                                                alert(response.msge);
                                                                                                                                                           }
                                                                                                                                                          });
                                             });
        <?php
             }
        ?>
        $('#destinatario').change(function(){
                                             $.post("/modelo/segvial/updcgo.php", {accion: 'sincnd', cnd:$(this).val()}, function(data){$('#siniestro').html(data);$('#siniestro').selectmenu({width: 450});})

                                             });
        $("#fservicio").focus();
        <?php if(!$edit){?>$.post("/modelo/segvial/updcgo.php", {accion: 'proxnrodcgo'}, function(data){$('#ndesc').val(data);});<?php }?>
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all"><?php print $edit?"Modificar Pedido de Explicacion":"Ingresar Pedido de Explicacion";?></legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td WIDTH="20%">Nro. Pedido de Expl. </td>
                                    <td colspan="2"><input id="ndesc" size="8" name="ndesc" readonly class="required ui-widget-content ui-corner-all" value="<?php echo $edit?$row['nro_descargo']:"";?>"/></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Emision</label></td>
                                    <td><input id="emision" name="emision" class="required ui-widget ui-widget-content  ui-corner-all" minlength="2" value="<?php echo $edit?$row['emision']:"";?>"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Destinatario</td>
                                    <td><select id="destinatario" name="destinatario" class="ui-widget-content  ui-corner-all">
                                                <?php //armarSelectCond($_SESSION['structure']);
                                                      if (isset($_GET[nro])){
                                                         $filter = "";
                                                      }
                                                      else{
                                                           $filter = "not borrado and activo";
                                                      }
                                                       armarSelect ('empleados', 'apellido', "id_empleado", "concat(apellido,', ',nombre)", $filter)
                                                ?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Siniestro Asociado</td>
                                    <td><select id="siniestro" name="siniestro" class="ui-widget-content  ui-corner-all">
                                                <option value='0'>NO DETALLA</option>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Entrega</label></td>
                                    <td><input id="entrega" name="entrega" class="ui-widget ui-widget-content  ui-corner-all" minlength="2" value="<?php echo $edit?$row['entrega']:"";?>"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Solicitado por...</td>
                                    <td><select id="solicitante" name="solicitante" class="ui-widget-content  ui-corner-all">
                                                <option value="0">NO DETALLA</option>
                                                <?php
                                                     armarSelect ('empleados', 'apellido', "id_empleado", "concat(apellido,', ',nombre)", "not borrado and activo and id_cargo not in (1, 5, 6, 7, 10, 11, 12)")

                                              //  armarSelectCond($_SESSION['structure']);?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Mediante...</label></td>
                                    <td><input id="mediante" size="50" name="mediante" class="ui-widget ui-widget-content  ui-corner-all" minlength="2" value="<?php echo $edit?$row['mediante']:"";?>"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Descripcion del hecho</label></td>
                                    <td><textarea id="desc_hecho" name="desc_hecho" rows="5" cols="50" class="required ui-widget ui-widget-content  ui-corner-all" ><?php echo $edit?$row['descripcion_hecho']:"";?></textarea></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Fecha Respuesta</label></td>
                                    <td><input id="rpta" name="rpta" class="ui-widget ui-widget-content  ui-corner-all" minlength="2" value="<?php echo $edit?$row['respuesta']:"";?>"/></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%">Resolucion</td>
                                    <td><select id="resolucion" name="resolucion" class="ui-widget-content  ui-corner-all">
                                                <option value="0"></option>
                                                <?php armarSelect('resolucionSiniestro', 'descripcion', 'id', 'descripcion');?>
                                        </select>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                                <tr>
                                    <td WIDTH="20%"><label for="fservicio">Detalle resolucion</label></td>
                                    <td><textarea id="detalleresolucion" name="detalleresolucion" rows="5" cols="50" class="ui-widget ui-widget-content  ui-corner-all" ><?php echo $edit?$row['detalle_resolucion']:"";?></textarea></td>
                                    <td></td>
                                </tr>

                                <tr>
                                   <?php
                                        $span=3;
                                        if ($edit) {
                                           $span=1;
                                        ?>
                                    <td colspan="2" align="right"><input id="print" class="boton" type="button" value="Guardar/Imprimir Resolucion" name="print"></td>
                                   <?php } ?>
                                    <td colspan="<?php echo $span;?>" align="right"><input id="envioFormulario" class="boton" type="submit" value="Guardar descargo" name="envioFormulario"></td>
                                </tr>
                                
                         </table>
	</fieldset>
 <input type="hidden" name="accion" id="accion" value="<?php print $edit?'upd':'sve';?>"/>
 <?php print $edit?'<input type="hidden" name="dcgo" id="dcgo" value="'.$_GET[nro].'"/>':'';?>
</form>
</div>

         

</BODY>
</HTML>
