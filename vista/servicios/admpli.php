<?php

     session_start();
     include_once('../main.php');
     include_once('../paneles/viewpanel.php');
     include_once('../../modelsORM/call.php');     
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
    
<script type="text/javascript" src="/vista/js/jquery.jeditable.js"></script>
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
                              $('#pl').selectmenu({width: 350}); 
                              $("#ok").button().click(function(){
                                                                

                                                                $.post('/vista/servicios/addtrfNew.php',
                                                                        {accion: 'add', cli: $('#clientes').val()},
                                                                        function(res){

                                                                                      $('#dats').html(res);

                                                                        })
                                                                        .fail(function(data){ alert(data);});
                              });
                              $('#pl').change(function(){
                                                            load();
                                                          }); 
    });

    function load()
    {
      $.post('/vista/servicios/addLnPl.php', {accion: 'ldln', pl:$('#pl').val()}, function(data){$('#clis').html(data); $('#clientes').selectmenu({width: 350}); });

    }
</script>

<body>
<?php
     menu();
     $planillas = call('PlanillaDiaria','findAll');
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Configurar parametros planilla</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Seleccionar opcion</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Planillas existentes</td>
                                    <td>
                                        <select id="pl" name="pl" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option>Seleccione una opcion</option>
                                                <?php
                                                    
                                                    foreach ($planillas as $value) {
                                                        print "<option value='".$value->getId()."'>$value</option>";
                                                    }
                                                ?>
                                        </select>
                                    </td>

                                    <td>Bloques definidos</td>
                                    <td id="clis"></td>
                                </tr>
                          </table>
                      </fieldset>
                         <div id="dats"></div>

            </fieldset>
         </form>

</body>
</html>

