<?php
error_reporting(0);
     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     include_once('../../modelsORM/controller.php');     
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);

     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>

<link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/DataTables/datatables.min.css" rel="stylesheet" />
<link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.css" />
<link rel="stylesheet" href="/vista/css/jquery.treetable.theme.default.css" />
    
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
<script src="/vista/js/jquery.treetable.js"></script>

<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/DataTables/datatables.min.js"></script>


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
.error{
  font-size:0.8em;
  color:#ff0000;
}

</style>
<script type="text/javascript">
    $(document).ready(function(){
                              $('#str').selectmenu({width: 350}); 
                              $("#ok").button().click(function(){
                                                                

                                                                $.post('/vista/servicios/addtrfNew.php',
                                                                        {accion: 'add', str: $('#str').val(), cli: $('#clientes').val()},
                                                                        function(res){

                                                                                      $('#dats').html(res);

                                                                        })
                                                                        .fail(function(data){ alert(data);});
                              });
                              $('#str').change(function(){
                                                            load();
                                                          });
                              load();
                              $('#str, #clientes').selectmenu({width: 200}); 
    });

    function load()
    {
      $.post('/vista/servicios/addtrfNew.php', {accion: 'ldcl', str:$('#str').val()}, function(data){$('#clis').html(data); $('#clientes').selectmenu({width: 350}); });

    }
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Configurar parametros facturacion</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Seleccionar Cliente</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Estructura</td>
                                    <td>
                                        <select id="str" name="str" class="ui-widget ui-widget-content  ui-corner-all">
                                                <?php
                                                    print listaEstructuras();// armarSelect('estructuras', 'nombre', 'id', 'nombre', "");
                                                ?>
                                        </select>
                                    </td>

                                    <td>Cliente</td>
                                    <td id="clis"></td>
                                </tr>
                          </table>
                      </fieldset>
                      <div id="facts"></div>
                         <div id="dats"></div>

            </fieldset>
         </form>

</body>
</html>

