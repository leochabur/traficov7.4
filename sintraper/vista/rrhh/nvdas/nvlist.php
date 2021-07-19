<?php
     session_start();
     include_once('../../main.php');
     include_once('../../paneles/viewpanel.php');
     include_once('../../../modelo/utils/dateutils.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
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
                          <?
                                                if (isset($_GET['emp'])){
                                                        print "$('#empleadores option[value=$_GET[emp]]').attr('selected', 'selected');
                                                               \$.post('/modelo/rrhh/nvdas/nvlist.php', {accion:'lemp', emplor:$_GET[emp], emple:$_GET[emple]}, function(data){\$('#empleados').html(data);});";
                                                }


                          ?>                           $('#empleadores ').selectmenu({width: 350});
                                                       $('#desde,#hasta').datepicker({dateFormat:'dd/mm/yy'});
                                                       $("#igual").button().click(function(){$("#hasta").val($("#desde").val());});
                                                       $("#cargar").button().click(function(){
                                                                                              var des = $("#desde").val();
                                                                                              var has = $("#hasta").val();
                                                                                              var emp = $("#emples").val();
                                                                                              $('#data').html("<div align='center'><img  alt='cargando' src='../../ajax-loader.gif' /></div>");
                                                                                              $.post("/modelo/rrhh/nvdas/nvlist.php", {accion:'lnov', emple: emp, desde:des, hasta:has}, function(data){$('#data').html(data);});
                                                       });
                                                       
                                                       <?
                                                       $desde = dateToJS("$_GET[ds]", "-");
                                                       $hasta = dateToJS("$_GET[hs]", "-");

                                                       if (isset($_GET['ds'])){
                                                          print "\$('#desde').val('$desde');
                                                                 \$('#hasta').val('$hasta');
                                                                 \$.post('/modelo/rrhh/nvdas/nvlist.php', {accion:'lnov', emple: \$('#emples').val(), desde:'$desde', hasta:'$hasta'}, function(data){\$('#data').html(data);});";
                                                       }
                                                       ?>
                                                       $('#empleadores').change(function(){
                                                                                            $.post("/modelo/rrhh/nvdas/nvlist.php", {accion:'lemp', emplor:$(this).val()}, function(data){$('#empleados').html(data);});
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Listado de Novedades</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar por:</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Empleadores</td>
                                    <td>
                                    <select id="empleadores" name="empleadores" class="ui-widget ui-widget-content  ui-corner-all">
                                            <option value="0">Todos</option>
                                                <?php

                                                 armarSelect('empleadores', 'razon_social', 'id', 'razon_social', "");
                                                ?>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td>Empleados</td>
                                    <td>
                                    <div id="empleados">
                                    </div>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20"></td>
                                    <td><input type="button" id="igual" value="<==>"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20"></td>
                                    <td>
                                        <input type="button" value="Cargar Novedades" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data">

                         </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
         </form>

</body>
</html>

