<?php
     session_start();

     include('../paneles/viewpanel.php');
     include('../main.php');
     define(RAIZ, '/nuevotrafico');

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>
 <script>
	$(function() {
        $('#unidades').selectmenu({width: 150});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );
        $("#desde, #hasta").datepicker({ dateFormat: "dd/mm/yy" });
		$("#pxd").button().click(function(){
                                                $.post("/modelo/segvial/vtouda.php", {accion: 'pxday', uda: $('#unidades').val(), dias: $('#dias').val()}, function(data) {$('#table').html(data);});
                                                });
		$("#rfe").button().click(function(){
                                                $.post("/modelo/segvial/vtouda.php", {accion: 'rafec', uda: $('#unidades').val(), hasta: $('#hasta').val(), desde: $('#desde').val()}, function(data) {$('#table').html(data);});
                                                });
	});
	</script>

<style type="text/css">
body { font-size: 72.5%; }
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Vencimientos Internos</legend>
		                 <div id="mensaje"></div>
                         <table border="0" align="center" width="50%">
                                <tr>
                                    <td WIDTH="20%">Internos</td>
                                    <td>
                                        <select id="unidades" name="unidades" class="ui-widget-content  ui-corner-all"  validate="required:true">
                                                <option value="0">Todas</option>
                                                <?php
                                                  armarSelect('unidades', 'CAST(interno as UNSIGNED)', 'id', 'interno', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td>
                                    </td>
                                    <td></td<
                                </tr>
                                <tr>
                                    <td>Proximos </td>
                                    <td><input type="text" id="dias" size="5" value="30" class="ui-widget ui-widget-content ui-corner-all"> Dias</td>
                                    <td></td>
                                    <td><input type="button" value="Cargar Datos" id="pxd"></td>
                                </tr>
                                <tr>
                                    <td>Rango Fechas </td>
                                    <td>Desde<input type="text" size="20" class="ui-widget ui-widget-content ui-corner-all" id="desde"></td>
                                    <td>Hasta<input type="text" size="20" class="ui-widget ui-widget-content ui-corner-all" id="hasta"></td>
                                    <td><input type="button" value="Cargar Datos" id="rfe"></td>
                                </tr>
                         </table>
	</fieldset>
	<input type="hidden" name="accion" id="accion" value="soser"/>
</form>
	</div>
	<div id="table">
	</div>
</div>

</BODY>
</HTML>
