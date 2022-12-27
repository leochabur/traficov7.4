<?php
     session_start();
     set_time_limit(0);
         error_reporting(E_ALL & ~E_NOTICE); 
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     include '../../../modelsORM/call.php';
     include_once '../../../modelsORM/controller.php';
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/js/DataTables/datatables.min.css" rel="stylesheet" />

  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/DataTables/datatables.min.js"></script>

  <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/utils.js"></script>

<link href="/vista/css/jquery.treetable.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
<script src="/vista/js/treetable/javascripts/src/jquery.treetable.js"></script>


<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }

.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $('#motivo').selectmenu({width: 350});
                                                       $('#coches, #uso').selectmenu({width: 200});
                                                       $("#upuda :button").button().click(function(){                                                                                          
                                                                                              $('#dats').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              

                                                                                              $.post("/modelo/taller/printpump.php",
                                                                                                     $('#upuda').serialize(),
                                                                                                     function(data){
                                                                                                                    $("#dats").html(data);

                                                                                                                    });
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Despachos recuperados de PUMP-CONTROL</legend>
		                 <div id="mensaje"> </div>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Desde</td>
                                    <td><input type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all" id="desde" name="desde"></td>
                                    <td>Hasta</td>
                                    <td><input type="text" size="20" class="ui-widget ui-widget-content  ui-corner-all" id="hasta" name="hasta"></td>
                                </tr>                       
                                <tr>
                                    <td colspan="4" align="left"><input type="button" value="Cargar Informe" id="load"></td>
                                </tr>
                         </table>
                         <br>
                         <div id="dats"></div>

            </fieldset>
            <input type="hidden" id="accion" name="accion" value="viewload">
         </form>

</body>
</html>

