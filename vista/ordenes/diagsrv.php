<?php
     session_start();


     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '');

     encabezado('Menu Principal - Sistema de Administracion - Campana');




     if (isset($_POST['fecha'])){
        $fec = $_POST['fecha'];
        $fecha = explode("/", $fec);
        $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
     }
     else
         $fecha = date("d/m/Y");

?>
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.multiselect.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.multiselect.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>



 <script>
    var ordenes;
	$(function(){
                 ordenes = new Array();
                 $("select").multiselect({multiple: false,
                                          header: "Seleccione una opcion",
                                          noneSelectedText: "Seleccione una opcion",
                                          selectedList: 1
                                          });
                 $('unidades').select();
                 $.mask.definitions['H']='[012]';
                 $.mask.definitions['N']='[012345]';
                 $(".hora").mask("H9:N9");
                 $("#apfil").toggle();
                 $('.chg').change(function(){ordenes = new Array(); $("#apfil").toggle();});
                 $('#loads').button().click(function(){
                                                     var clientes = $("#clientes").val();
                                                     var origenes = $("#origenes").val();
                                                     var destinos = $("#destinos").val();
                                                     var hd = $('#h-desde').val();
                                                     var hh = $('#h-hasta').val();
                                                     var cron = $('#name-c').val();
                                                     $('#ressrv').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                     $.post('/modelo/servicios/diagsrv.php', {accion: "fsrv", shw:$(':radio:checked').val(),ncron:cron, dsd: hd, hst:hh, org:origenes, dst:destinos, cli:clientes}, function(data){
                                                                                                                                                                                                                                    $('#ressrv').html(data);

                                                                                                                                                                                                                                   });

                                                     });
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#svsrv').button().click(function(){

                                                       $.post('/modelo/servicios/diagsrv.php', {accion: "dss", 
                                                                                                ords: ordenes.join(','), 
                                                                                                interno: $('#interno').val(), 
                                                                                                fecd:$("#fecha").val()}, 
                                                                                                function(data){
                                                                                                                    console.log(data);
                                                                                                                    $( "#fedisr" ).dialog( "close" );
                                                                                                                    $('#ressrv').html('');
                                                                                                                });
                                                       });
                 $('#diagsrv').button().click(function(){
                                                         if (ordenes.length > 0){
                                                                 $( "#fedisr" ).dialog( "open" );
                                                         }
                                                         else{
                                                              alert('No ha seleccionado ningun servisio!');
                                                         }
                                                         });
                 $( "#fedisr" ).dialog({
                                        autoOpen: false,
                                        height: 300,
                                        width: 450,
                                        modal: true
                                        });

	});
	
    function checkTodos (obj) {
             ordenes = new Array();
             if (obj.checked){
                   $( "#tablita td input:checkbox" ).each(function (){
                                                                     ordenes.push(this.id);
                                                                   });
             }
             $("#tablita input:checkbox").attr('checked', obj.checked);
    }

    if (!Array.indexOf) {
        Array.prototype.indexOf = function (obj, start) {
                                for (var i = (start || 0); i < this.length; i++) {
                                    if (this[i] == obj) {
                                       return i;
                                    }
                                }
                                return -1;
                                }
    }
    
    function cargarCheck(orden){
             if (orden.checked){
                ordenes.push(orden.id);
             }
             else{
                  var a = ordenes.indexOf(orden.id);
                  ordenes.splice(a,1);
             }
    }



	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}



</style>
<BODY>
<?php
     menu();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Diagramar Servicios</legend>
         <hr align="tr">
         <table size="100%">
         <tr>
             <td colspan="2"><input type="radio" checked name="filter" class="chg" value='all'>Mostrar todos los Servicios&nbsp;&nbsp;<input type="radio" class="chg" name="filter" value='filter'>Filtrar Servicios</td>
             <td colspan="2"></td>
         </tr>
         <tr>
         <td colspan="2">
             <div id="apfil">
              <table  align="center" border="0">
                     <tr>
                         <td>Clientes</td>
                         <td>
                             <select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                             </select>
                         </td>
                         <td>Origen</td>
                         <td>
                             <select id="origenes" name="origenes"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                             </select>
                         </td>
                         <td>Destino</td>
                         <td>
                             <select id="destinos" name="destinos"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <?php
                                                     armarSelect('ciudades', 'ciudad', 'id', 'ciudad', "(id_estructura = $_SESSION[structure])");
                                                ?>
                             </select>
                         </td>
                     </tr>
                     <tr>
                         <td>Hora Citacion desde</td>
                         <td><input id="h-desde" type="text" class="hora" size="5"></td>
                         <td>Hora citacion hasta</td>
                         <td><input id="h-hasta" type="text" class="hora" size="5"></td>
                         <td>Parte nombre contenga</td>
                         <td><input id="name-c" type="text"></td>
                     </tr>
              </table>
              </div>
         </td>
         </tr>
         </table>
         <hr align="tr">
         <table>
                      <tr>
                         <td colspan="2" align="right"><div id="loads">Cargar Servicios</div></td>
                     </tr>
         </table>
         <hr align="tr">
         <div id="ressrv">
         </div>
         <div>
              <input type="button" id="diagsrv" value="Diagramar Servicios Seleccionados">
         </div>
	</fieldset>
	<div id="fedisr" title="Fecha diagrama">
      <form id="fdsrv">
         <div>
                <label for="fecha">Fecha</label>
                <input type="text" size="20" id="fecha" class="required ui-widget ui-widget-content  ui-corner-all">
        </div>
        <br>
        <br>
         <div>
                <label for="interno">Interno</label>
            <?php
              $sqlIntenos = "select id, interno from unidades where id_estructura = $_SESSION[structure] and activo order by interno";
              $intenos = ejecutarSQL($sqlIntenos);

              $select = "<select name='interno' id='interno' class='unidades'>";
              while($row = mysql_fetch_array($intenos))
              {
                    $select .= "<option value='$row[id]'>$row[interno]</option>";
              }
              $select .= "</select>";
              print $select;
            ?>

         </div>
         <br>
         <div id="svsrv">Diagramar Servicios</div>
      </form<
	</div>
</BODY>
</HTML>
