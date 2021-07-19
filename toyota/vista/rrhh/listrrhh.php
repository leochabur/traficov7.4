<?php
     session_start();
     include('../main.php');
     include('../paneles/viewpanel.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
     $opcion = getOpcion('sel-combo-def', $_SESSION['structure']);
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/blue/style.css" rel="stylesheet"/>
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="<?php echo RAIZ;?>/vista/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.dataTables.css" rel="stylesheet" />
  
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.dataTables.min.js"></script>

<style type="text/css">
body { font-size: 82.5%; }
label { display: inline-block; width: 250px; }
legend { padding: 0.5em; }
fieldset fieldset label { display: block; }
td{padding: 0px;}
.small.button, .small.button:visited {
font-size: 11px ;
}

input.text { margin-bottom:12px; width:95%; padding: .4em; }
#newuda .error{
	font-size:0.8em;
	color:#ff0000;
}
table tr td{padding: 3px;}
</style>
<script type="text/javascript">
                          $(document).ready(function(){
                                                       $("#cargar").button().click(function(){
                                                                                              var datos = $("#upuda").serialize();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/rrhh/listrrhh.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       $('select').selectmenu({width: 350});

                          });
</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Listado de Personal</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Personal de:</legend>
                         <table border="0" align="center" width="75%" name="tabla">
                                <tr>
                                    <td>Empleadores<select id="empleador" name="empleador" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="0">Todos</option>
                                                <?php
                                                     armarSelect('empleadores', 'razon_social', 'id', 'razon_social', "(id in (SELECT id_empleador FROM empleadoresporestructura where id_estructura = $_SESSION[structure]))");
                                                ?>
                                        </select>
                                    </td>
                                    <td>Ordenear por: Apellido<input name="order" checked value="apenom" type="radio"> Legajo<input name="order" value="legajo" type="radio"></td>
                                    <td>
                                        <input type="button" value="Cargar" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data"> </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
         </form>

</body>
<?php
          print "<script type=\"text/javascript\">
                    \$(\"#empleador> option[value=$opcion]\").attr(\"selected\", \"selected\");
                    \$('#data').html(\"<div align='center'><img  alt='cargando' src='../ajax-loader.gif' /></div>\");
                    \$.post(\"/modelo/rrhh/listrrhh.php\", {accion:\"list\", empleador:$opcion, order:\"apenom\"}, function(data){\$(\"#data\").html(data);});
                </script>";
?>
</html>

