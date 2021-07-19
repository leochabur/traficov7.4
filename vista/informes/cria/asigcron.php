<?php
error_reporting(E_ALL & ~E_NOTICE);
     session_start();
     include_once('../../../main.php');
     include_once('../../../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     include('../../../modelsORM/controller.php');
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.css" />
    <link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
    <link type="text/css" href="/vista/css/estilos.css" rel="stylesheet" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
        <script src="/vista/js/jquery.treetable.js"></script>

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
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}

</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#save").button().click(function(){
                                                                                              var btn = $(this);
                                                                                  $('#arts').html('');
                                                                                              var data = $("#upuda").serialize();
                                                                                              $.post("/modelo/informes/cria/factvta.php",
                                                                                                     data,
                                                                                                     function(dats){
                                                                                                                    $('#arts').html(dats);
                                                                                                                    });
                                                       });
                                                       
                                                       $('#clientes').selectmenu({width: 350});


                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Asignacion tarifa/servicio</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Cliente</td>
                                    <td>
                                        <select id="clientes" name="clientes">
                                                <?php
                                                     print clientesOptions();
                                                ?>
                                        </select>
                                    </td>
                                    <td colspan="2" align="right"><input type="button" value="Cargar servicios" id="save"></td>
                                </tr>

                         </table>
                         <div id="arts"> </div>

            </fieldset>
            <input type="hidden" name="accion" value="loadascr">
         </form>

</body>
</html>

