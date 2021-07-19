<?php
session_start();
set_time_limit(0);
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
    // include_once('../paneles/viewpanel.php');
    include('../../controlador/bdadmin.php');
     include_once('../main.php');

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
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet"/>
 <link href="<?php echo RAIZ;?>/vista/css/jquery.contextMenu.css" rel="stylesheet" type="text/css" />
 <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.contextMenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/scroll-table/jquery.chromatable.js"></script>
 <script>
	$(function(){
                 $(':submit').button();
                 $('table a').button();
                 $('#fecha').datepicker({dateFormat:'dd/mm/yy'});
                 $('#mincorte').mask('999');
                 $('#cargar').click(function(event){
                                             event.preventDefault();
                                             $.post('/modelo/ordenes/smlvacv3.php',
                                                    {accion:'vvc', fecha:$('#fecha').val()},
                                                    function(data){
                                                                   var response = $.parseJSON(data);
                                                                   if (response.status){
                                                                      if (confirm('Los Vacios para el dia '+$('#fecha').val()+' ya han sido generados!, Volver a generarlos?')){
                                                                        $('#simul').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                        $.post('/modelo/ordenes/smlvacv3.php', $('#load').serialize() , function(datos) {$('#simul').html(datos);});
                                                                      }
                                                                   }
                                                                   else{
                                                                        $('#simul').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                        $.post('/modelo/ordenes/smlvacv3.php', $('#load').serialize() , function(datos) {$('#simul').html(datos);});
                                                                   }
                                                                   });
                                             });
                                             
                  $('#cnsvac').click(function(event){
                                          event.preventDefault();
                                          var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                          dialog.dialog({
                                                         close: function(event, ui) {dialog.remove();},
                                                         title: 'Liquidacion de Conductores',
                                                         width:850,
                                                         height:450,
                                                         modal:true,
                                                         show: {
                                                               effect: 'blind',
                                                               duration: 300
                                                               },
                                                         hide: {
                                                               effect: 'blind',
                                                               duration: 300
                                                               }
                                                         });
                                          dialog.load('/vista/ordenes/diagvccnd.php',{orden:''},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
                                          });

                 $('#renombrar').click(function(event){
                                             event.preventDefault();
                                             if (confirm('Las ordenes del cliente TOYOTA del dia '+$('#fecha').val()+' seran actualizadas segun los criterios establecidos. Continuar?'))
                                             {
                                                $('#simul').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                 $.post('/modelo/ordenes/smlvacv3.php',
                                                        {accion:'renord', fecha:$('#fecha').val()},
                                                        function(data){
                                                                          $('#simul').html(data);
                                                                       });
                                              }
                                             });
                                              

	});
	




	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
.small.button, .small.button:visited {
font-size: 11px ;
}

#upcontact div{padding: 2px;}
#cargar, #cnsvac, #mapa, #renombrar {font-size: 72.5%;}


option.0{background-color: #;}
option.1{background-color: ;}

</style>
<BODY>
<?php
     menu();
     $con = conexcion();
?>
    <br><br>
    <div id="result"></div>
    <fieldset class="ui-widget ui-widget-content ui-corner-all">

         <legend class="ui-widget ui-widget-header ui-corner-all">Diagramar Servicios Vacios V 3.0</legend>
         <hr align="tr">
         <div>
         <form id="load" method="post" autocomplete="off">
               <table border="0" width="65%" align="center">
                      <tr>
                          <td colspan="2">Vacios del dia</td>
                          <td colspan="2"><input id="fecha" name="fecha" type="text" size="15"></td>
                      </tr>
                      <tr>
                          <td colspan="2">Solo simular(No genera ninguna orden)</td>
                          <td colspan="2"><input type="checkbox" name="simula"></td>
                      </tr>
                      <tr>
                          <td colspan="2">Minutos citacion</td>
                          <td colspan="2"><select size="1" id="mins" name="mins">
                                      <option value="30">30</option>
                              </select>
                          </td>
                      </tr>
                      <tr>
                          <td colspan="2">Minutos Corte</td>
                          <td colspan="2">
								              <input type="text" name="mincorte" id="mincorte" value="480" size="5">
                          </td>
                      </tr>                      
                      <tr>
                          <td align="left"><a href="../../vista/ordenes/ubicaciones.php" name="mapa" id="mapa" target="_blank">Ver mapa de ubicaciones</a></td>
                          <td align="left"><input type="submit" id="cnsvac" name="cnsvac" class="button" value="Conductores sin vacio"></td>
                          <td align="left"><input type="submit" id="cargar" name="cargar" class="button" value="Diagramar Vacios"></td>
                          <td></td>
                          <!--td align="left"><input type="submit" id="renombrar" name="renombrar" class="button" value="Renombrar ordenes"></td-->
                      </tr>
               <table>
              <input type="hidden" name="accion" id="accion" value="sml">
         </form>
         </div>
         <hr align="tr">
         <div id="simul"></div>
	</fieldset>
	

</BODY>
</HTML>
