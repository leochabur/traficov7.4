<?php

     include('../main.php');
     include('../paneles/viewpanel.php');
     include_once('../../controlador/ejecutar_sql.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     $vacio = getOpcion('empleador-default', $_SESSION['structure']);

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

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }



</style>
<script type="text/javascript">
        $(document).ready(function(){
                                     $("#empleador option[value=<?print $vacio;?>]").attr("selected",true);
                                     $('#empleador').selectmenu({width: 350});
                                     $.mask.definitions['m']='[01]';
                                     $('#desde, #hasta').mask("m9/2099");
                                     $('#cargar').button().click(function(){
                                                                            $('#sbana').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                            $.post("/modelo/rrhh/lstinc.php", {accion: 'sbana', desde:$('#desde').val(), hasta:$('#hasta').val()}, function(data){
                                                                                                                                                $('#sbana').html(data);
                                                                                                                                                });
                                                                            });
                                                       });
</script>

<body>
<?php
     menu();
?>
    <br><br>
	<div id="tabs-1" class="ui-state-highlight ui-corner-all">
         <form class="cmxform" id="commentForm">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Ver Incentivos</legend>
                         <table border="0" align="center" name="tabla">
                                <tr>
                                    <td>
                                        Mes/A<?print htmlentities('ñ');?>o Desde
                                    </td>
                                    <td>
                                        <input id="desde" name="desde" size="8" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/>
                                    </td>
                                    <td>
                                        Mes/A<?print htmlentities('ñ');?>o Hasta
                                    </td>
                                    <td>
                                        <input id="hasta" name="hasta" size="8" class="ui-widget ui-widget-content  ui-corner-all" minlength="2"/>
                                    </td>
                                    <td><input type="button" id="cargar" value="Cargar Datos"/> </td>
                                </tr>
                         </table>
                         <div id="sbana">
                         </div>
            </fieldset>
         </form>
</body>
</html>

