<?php
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#upuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#fpago').datepicker({dateFormat:'yy-mm-dd'});
                                                       $("#cargar").button();
                                                       $('#importe').mask("999999.99");
                                                       $('#str').change(function(){
                                                                                  $.post("/modelo/turismo/srvlst.php",{accion:'ldcli', str:$('#str').val()}, function(data){
                                                                                                                                                                                   $('#clis').html(data);
                                                                                                                                                                                   });
                                                                                  });
                                                       $("#str option[value=1]").attr("selected", "selected");
                                                       $('#str, #type').selectmenu({width: 350});
                                                       $.post("/modelo/turismo/srvlst.php",{accion:'ldcli', str:$('#str').val(), all:'nook'}, function(data){$('#clis').html(data);});
                                                       $('#upuda').validate({
                                                                               submitHandler: function(){
                                                                                                         var datos = $('#upuda').serialize();
                                                                                                         datos+="&accion=svepgo";
                                                                                                         if ($('#affc').is(':checked')){
                                                                                                                var suma = 0;
                                                                                                                var facts = '';
                                                                                                                $('#faccli input:checkbox:checked').each(function(){
                                                                                                                                                                    suma++;
                                                                                                                                                                    var id = $(this).attr('id');
                                                                                                                                                                    if (facts == '')
                                                                                                                                                                       facts = id;
                                                                                                                                                                    else
                                                                                                                                                                        facts = facts + ','+id;
                                                                                                                                                                    });
                                                                                                                if (suma){
                                                                                                                   datos+="&facts="+facts;
                                                                                                                   $.post("/modelo/turismo/addnroc.php", datos , function(data){alert(data);});
                                                                                                                }
                                                                                                                else{
                                                                                                                     alert('No ha seleccionado ninguna factura!');
                                                                                                                }
                                                                                                          }
                                                                                                          else{
                                                                                                               $.post("/modelo/turismo/addnroc.php", datos , function(data){alert(data);});
                                                                                                          }
                                                                                                         }
                                                                               });
                                                       $('#affc').click(function(){
                                                                                   if ($(this).is(':checked')){
                                                                                      var datos = $("#upuda").serialize();
                                                                                      $('#dats').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                      $.post("/modelo/turismo/addnroc.php",datos , function(data){$('#dats').html(data);});
                                                                                   }
                                                                                   else{
                                                                                        $('#dats').empty();
                                                                                   }
                                                                                   });
                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Cargas pagos Cliente</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Servicios</legend>
                         <table border="0" align="center" width="70%" name="tabla">
                                <tr>
                                    <td>Estructura</td>
                                    <td>
                                        <select id="str" name="str" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                     armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                                ?>
                                        </select>
                                    </td>

                                    <td>Cliente</td>
                                    <td id="clis">
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Fecha de Pago</td>
                                    <td><input id="fpago" name="fpago"  type="text" size="20" class="required" ></td>
                                    </tr>
                                    <tr>
                                    <td>Importe</td>
                                    <td><input id="importe" name="importe" type="text" size="20" class="required" ></td>
                                    </tr>
                                    <tr>
                                    <td>Afectar facturas</td>
                                    <td><input type="checkbox" id="affc"></td>
                                    </tr>
                                    <tr>
                                    <td colspan="4" align="right">
                                        <input type="submit" value="Guardar Pago" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="ldpgo">
            <input type="hidden" name="order" id="order">
         </form>

</body>
</html>

