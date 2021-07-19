<?php
     session_start();
   /*  include('../../modelo/provincia.php');
     include('../../modelo/ciudades.php');     */
     include('../../paneles/viewpanel.php');
     include('../../main.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>
 <script>
	$(function() {
        $('#savesrv').button();
        $('select').selectmenu({style:'popup',
                                width: 450});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );

        $.editable.addInputType('masked', {element : function(settings, original) {   var input = $('<input />').mask(settings.mask);
                                                                                      $(this).append(input);
                                                                                      return(input);
                                                                                   }
                                });
        
        $("#cliente").change(function(){
                                         var cliente = $("#cliente option:selected").val();
                                         $.post("../../../modelo/clientes/loaddatacliente.php",{cli: cliente, accion: 'load'},function(data){
                                                                                                                      $("#datacli").html(data);
                                                                                                                     })
                                         });
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Modificar Datos de Clientes</legend>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td>
                                        <select id="cliente" name="cliente" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">Seleccione un Cliente</option>
                                                <?php
                                                  armarSelect('clientes', 'razon_social', 'id', 'razon_social', "(id_estructura = $_SESSION[structure])");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                         </table>
                         <div id="datacli">
                         </div>
	</fieldset>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

</BODY>
</HTML>
