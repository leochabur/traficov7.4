<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');     */
     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />

 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>


 

 <script>
	$(function() {
        $('#savesrv').button();
        $('select').selectmenu({width: 350});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );

        
        $("#cliente").change(function(){
                                         var cliente = $("#cliente option:selected").val();
                                         $('#serv-cli').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                         $('#srvcron').empty();
                                         $.post("/modelo/servicios/ctrldiag.php",{cli: cliente, accion: 'lcr'},function(data){
                                                                                                                    $("#serv-cli").html(data);
                                                                                                                   });
                                        });
                                        
        $("#perfiles").change(function(){
                                         var ctl = $(this).val();
                                         $('#srvctrl').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                         $.post("/modelo/servicios/ctrldiag.php",{ctrl: ctl, accion: 'lsrvctrl'},function(data){
                                                                                                                    $("#srvctrl").html(data);
                                                                                                                   });
                                        });

	});
	</script>

<style type="text/css">

table { font-size: 80%; }
label { display: inline-block; width: 250px; }
legend {padding: 0.5em;}
fieldset fieldset label { display: block; }



#addserv .error{
	font-size:0.8em;
	color:#ff0000;
}

#upnwserv .error{
	font-size:0.8em;
	color:#ff0000;
}

#upcontact .error{
	font-size:0.8em;
	color:#ff0000;
}

fieldset .div {
  padding-top: 7px;
  padding-right: 7px;
  padding-bottom: 7px;
  padding-left: 7px;
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Control de diagramas</legend>
       	                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                           <legend class="ui-widget ui-widget-header ui-corner-all">Perfiles creados</legend>
                                           <table align="LEFT">
                                                  <tr>
                                                      <td>Perfiles Creados</td>
                                                      <td>
                                                          <select id="perfiles" name="perfiles" title="Please select something!" validate="required:true">
                                                                  <option value="0">Seleccionar uno</option>
                                                                  <?php
                                                                       armarSelect('controlDiagramas', 'id', 'id', 'nombre', "(id_estructura = $_SESSION[structure])");
                                                                  ?>
                                                          </select>
                                                      </td>
                                                  </tr>
                                           </table>
                         </fieldset>
 	                     <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                           <legend class="ui-widget ui-widget-header ui-corner-all">Agregar Servicios</legend>
                                                          <table border="0" align="LEFT" width="100%">
                                                                 <tr>
                                                                     <td>Cliente</td>
                                                                     <td>
                                                                         <select id="cliente" name="cliente" title="Please select something!" validate="required:true">
                                                                                 <option value="0">Todos los clientes</option>
                                                                                 <?php
                                                                                      armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure]) and (activo)");
                                                                                 ?>
                                                                         </select>
                                                                     </td>
                                                                     <td>
                                                                     </td>
                                                                 </tr>
                                                                  <tr>
                                                                      <td>
                                                                      </td>
                                                                      <td>
                                                                          <div id="serv-cli"></div>
                                                                      </td>
                                                                  </tr>
                                                                  <tr>
                                                                     <td></td>
                                                                     <td>
                                                                         <div id="srvcron"></div>
                                                                     </td>
                                                                  </tr>
                                                          </table>
                         </fieldset>
   	                     <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                           <legend class="ui-widget ui-widget-header ui-corner-all">Servicios cargados</legend>
		                           <div id="srvctrl">
		                           </div>
                         </fieldset>
	</fieldset>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

</BODY>
</HTML>
