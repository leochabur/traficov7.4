<?php
     session_start();
     include('../main.php');
     include_once('../../controlador/bdadmin.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
     encabezado('Menu Principal - Sistema de Administracion - Campana');
?>
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/jquery.ui.selectmenu.css" rel="stylesheet" />
   <link type="text/css" href="<?php echo RAIZ;?>/vista/css/estilos.css" rel="stylesheet" />
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="<?php echo RAIZ;?>/vista/js/jquery.ui.datepicker-es.js"></script>

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
                                                                                              $.post("/modelo/rrhh/stctacteff.php", datos, function(data){$('#data').html(data);});
                                                       });
                                                       $('#mes_d, #mes_h, #emples').selectmenu({width: 250});
                                                       $('#anio_d, #anio_h').selectmenu({width: 100});

                          });

</script>

<body>
<?php
     menu();
?>
    <br><br>
         <form id="upuda">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Gestion Francos/Feriados Personal</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar</legend>
                         <table border="0" align="center" width="30%" name="tabla">
                                <tr>
                                   <td>Empleador</td>
                                   <td colspan="2">
                                      <select name="empleador" id="emples">
                                      <option value="1">Master Bus</option>
                                      <option value="51">Sintra</option>
                                      </select>

                                   </td>
                                   
                                   <!--tr>
                                    <td>Desde</td>
                                    <td>
                                        <select id="mes_d" name="mes_d" class="ui-widget ui-widget-content  ui-corner-all">
                                          <option value="1">Enero</option>                                          
                                          <option value="2">Febrero</option>
                                          <option value="3">Marzo</option>
                                          <option value="4">Abril</option>
                                          <option value="5">Mayo</option>
                                          <option value="6">Junio</option>
                                          <option value="7">Julio</option>
                                          <option value="8">Agosto</option>
                                          <option value="9">Septiembre</option>
                                          <option value="10">Octubre</option>
                                          <option value="11">Noviembre</option>
                                          <option value="12">Diciembre</option>
                                        </select>
                                    </td>
                                    <td>
                                          <select id="anio_d" name="anio_d" class="ui-widget ui-widget-content  ui-corner-all">
                                          <?php
                                         /*     $year = date('Y');
                                              $yearmas = ($year+1);
                                              $yearmenos = ($year-1);
                                              print "<option value='$year'>$year</option>";
                                              print "<option value='$yearmenos'>$yearmenos</option>";
                                              print "<option value='$yearmas'>$yearmas</option>";*/
                                          ?>
                                          </select>
                                    </td>
                                  </tr-->
                                  <!--tr>
                                    <td>Hasta</td>
                                    <td>
                                        <select id="mes_h" name="mes_h" class="ui-widget ui-widget-content  ui-corner-all">
                                          <option value="1">Enero</option>                                          
                                          <option value="2">Febrero</option>
                                          <option value="3">Marzo</option>
                                          <option value="4">Abril</option>
                                          <option value="5">Mayo</option>
                                          <option value="6">Junio</option>
                                          <option value="7">Julio</option>
                                          <option value="8">Agosto</option>
                                          <option value="9">Septiembre</option>
                                          <option value="10">Octubre</option>
                                          <option value="11">Noviembre</option>
                                          <option value="12">Diciembre</option>
                                        </select>
                                    </td>
                                    <td>
                                          <select id="anio_h" name="anio_h" class="ui-widget ui-widget-content  ui-corner-all">
                                          <?php
                                            /*  $year = date('Y');
                                              $yearmas = ($year+1);
                                              $yearmenos = ($year-1);
                                              print "<option value='$year'>$year</option>";
                                              print "<option value='$yearmenos'>$yearmenos</option>";
                                              print "<option value='$yearmas'>$yearmas</option>";*/
                                          ?>
                                          </select>
                                    </td>
                                  </tr-->
                                
                                    <td colspan="3" align="right">
                                        <input type="button" value="Cargar" id="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <div id="data"> </div>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="load">
         </form>
</body>
</html>

