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
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.jeditable.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.tablesorter.js"></script>

 

 <script>
	$(function() {
        $('#savesrv').button();
        $('select').selectmenu({width: 350});
        $.datepicker.setDefaults( $.datepicker.regional[ "es" ] );

        
        $("#cliente").change(function(){
                                         var cliente = $("#cliente option:selected").val();
                                         $('#serv-cli').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                         $.post("../paneles/loadpstcr.php",{cli: cliente, accion: 'lcr'},function(data){
                                                                                                                        $("#serv-cli").html(data);
                                                                                                                        $('#servicios').selectmenu({width: 450});
                                                                                                                     })



                                         });

	});
	</script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend {padding: 0.5em;}
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}
.button-small{font-size: 62.5%;}
div#users-contain {margin: 20px 0; font-size: 62.5%;}
div#users-contain table { margin: 1em 0; border-collapse: collapse; width: 100%; }
div#users-contain table td, div#users-contain table th { border: 1px solid #eee; padding: .6em 10px; text-align: left; }
//input.text { margin-bottom:12px; width:95%; padding: .4em; }
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

form table td{
  padding-top: 3px;
  padding-right: 3px;
  padding-bottom: 3px;
  padding-left: 3px;
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Agregar Marcas de Horario al Cronograma</legend>
                         <table border="0" align="center" width="75%">
                                <tr>
                                    <td WIDTH="20%">Cliente</td>
                                    <td>
                                        <select id="cliente" name="cliente" title="Please select something!" validate="required:true">
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
                                    <td WIDTH="20%">Cronogramas</td>
                                    <td>
                                        <div id="serv-cli">

                                        </div>
                                    </td>
                                    <td>

                                    </td>
                                </tr>
                         </table>
                         <div id="hora-servi">
                         </div>
	</fieldset>
</form>
	</div>
	<div id="tabs-2" class="ui-state-highlight ui-corner-all">
	</div>
</div>

</BODY>
</HTML>
