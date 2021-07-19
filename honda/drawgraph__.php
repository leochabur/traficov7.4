<?php
     session_start();

     include( 'GoogChart.class.php' );
     include( 'bdadmin.php' );
     include( 'dateutils.php' );
     include('./main.php');
     include('./core_chart.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php

     encabezado('Menu Principal - Sistema de Administracion - Campana');
     
        if (isset($_POST['city_origen'])){
           $origen = $_POST['fcity_origen'];
        }
        if (isset($_POST['city_destino'])){
           $destino = $_POST['fcity_destino'];
        }
        if (isset($_POST['servicios'])){
           $servicio = $_POST['fservicios'];
        }

?>

   <link type="text/css" href="../toyota/css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="../toyota/css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="../toyota/css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="../toyota/css/jquery.dataTables.css" rel="stylesheet" />
    
<script type="text/javascript" src="../toyota/js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="../toyota/js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="../toyota/js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="../toyota/js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="../toyota/js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="../toyota/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>


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
                                                       $('#cargar').button();
                                                       $( "#center, #vuelta" ).tabs();
                                                       <?php
                                                            if ($origen){
                                                               print "\$('#fcity_origen option[value=$origen]').attr('selected', 'selected');
                                                                       \$('#city_origen').prop( 'checked', true );";
                                                            }
                                                            else{
                                                                 print "\$('#city_origen').prop( 'checked', false );
                                                                        \$('#fcity_origen').attr('disabled', 'disabled');";
                                                            }
                                                            if ($destino){
                                                               print "\$('#fcity_destino option[value=$destino]').attr('selected', 'selected');
                                                                       \$('#city_destino').prop( 'checked', true );";
                                                            }else{
                                                                 print "\$('#city_destino').prop( 'checked', false );
                                                                        \$('#fcity_destino').attr('disabled', 'disabled');";
                                                            }

                                                       ?>
                                                       

                                                       <?php if (isset($_POST['cargar'])){?>
                                                       
                                                             $("#tipo option[value='<?php echo $_POST['tipo'];?>']").attr('selected', 'selected');
                                                             $('#tipo, #turno, #clis, #fcity_destino, #fcity_origen').selectmenu({width: 350});
                                                       
                                                       <?php  } else {


                                                              print "\$('#tipo option[value=$_POST[tipo]]').attr('selected', 'selected');";

                                                              print "\$('#tipo, #turno, #clis, #fcity_destino, #fcity_origen').selectmenu({width: 350});";

                                                       } ?>
                                                       
   $.post('./load_data.php',{accion:'lcd', trn:$('#turno').val(), tpo: $('#tipo').val()},function(data){$('#cronos').html(data);}).done(function() {
    <?php
                                                            if ($servicio){
                                                               print "\$('#fservicios option[value=$servicio]').attr('selected', 'selected');
                                                                      \$('#servicios').prop( 'checked', true );
                                                                      \$('#fservicios').selectmenu({width: 350});";
                                                            }
                                                            else{
                                                                 print "\$('#servicios').prop( 'checked', false );
                                                                        \$('#fservicios').attr('disabled', 'disabled');
                                                                        \$('#fservicios').selectmenu({width: 350});";
                                                            }
    ?>
  });
  
  $('.city').click(function(){
                                  var id = $(this).attr('id');
                                  if( $(this).attr('checked')){
                                      $("#servicios").prop( "checked", false );
                                      $("#f"+id).removeAttr('disabled');
                                      $("#fservicios").attr('disabled', 'disabled');
                                  }
                                  else{
                                      $("#f"+id).prop('disabled', 'disabled');
                                  }
                                  $(".selec").selectmenu({width: 350});
                                  });

                                  
  $('#servicios').click(function(){
                                    if( $(this).attr('checked')){
                                        $("#fservicios").removeAttr('disabled');
                                        $("#fcity_origen, #fcity_destino").prop('disabled', 'disabled');
                                        $(".city").prop( "checked", false );
                                    }
                                    else{
                                         $("#fservicios").prop('disabled', 'disabled');
                                    }
                                    $(".selec").selectmenu({width: 350});
                                    });
  $('#tipo').change(function(){
                               var check = $("#servicios").prop( "checked");
   $.post('./load_data.php',{accion:'lcd', trn:$('#turno').val(), tpo: $('#tipo').val()},function(data){$('#cronos').html(data);}).done(function() {
    if (!check)
    $("#fservicios").prop('disabled', 'disabled');
    $('#fservicios').selectmenu({width: 350});
  });
                               });


                          });
</script>



<body>
<?php
     menu();

     $chcnf = "";
     $chefc = "checked";
     if (isset($_POST['cargar'])){
        $title = "Graficos de Confiabilidad";
        $desde = dateToMysql($_POST['desde'], '/');
        $hasta = dateToMysql($_POST['hasta'], '/');
        $t_serv = $_POST['tipo'];
        $turno =  $_POST['turno'];
        $tg = "ADMINISTRACION";
        if ($t_serv == 2)
           $tg = "PRODUCCION";
        if ($turno == 1)
           $tg.=" TURNO MAÑANA";
        elseif ($turno == 2){
               $tg.=" TURNO TARDE";
        }
        else
            $tg.=" TURNO NOCHE";
           
        if ($_POST['cnf'] == 'cnf'){
           $title = "Graficos de Confiabilidad";
           $chcnf = "checked";
           $chefc = "";
           $titulo = titulo($servicio,$origen,$destino);
           $datosIda = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'i','');
         //  print $datosIda;
           $tablaEntrada = $datosIda[1];
           $datos = $datosIda[0];
           $soloEntrada = true; //flag para indicar si se debe graficar la vuelta tambien
           if ((!$servicio) && (!$origen) && (!$destino)){ //se grafican los graficos de ida y vuelta
              $datosVta = graphConfiabilidad($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'v');
              $tablaSalida = $datosVta[1];
              $datosvta = $datosVta[0];
              $chartIda = createFunctionChart("drawChartVta", $datosvta, "egr", "Salida de Planta");
              $soloEntrada = false;
              $tg = htmlentities($tg);
           }
           else{
                $tg = $titulo;
           }

           $chartVta = createFunctionChart("drawChart", $datos, "ing", "");
           print "<script type=\"text/javascript\">
                          google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
                          google.setOnLoadCallback(drawChart);
                          google.setOnLoadCallback(drawChartVta);
                          $chartIda
                          $chartVta
          </script>";
        }
        else{
           $chcnf = "";
           $chefc = "checked";
           $title = "Graficos de Eficiencia";
           
           $titulo = titulo($servicio,$origen,$destino);
           $datosIda = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'i','');
         //  print $datosIda;
           $tablaEntrada = $datosIda[1];
           $datos = $datosIda[0];
           $soloEntrada = true; //flag para indicar si se debe graficar la vuelta tambien
           if ((!$servicio) && (!$origen) && (!$destino)){ //se grafican los graficos de ida y vuelta
              $datosVta = graphEficiencia($_POST['desde'], $_POST['hasta'], $origen, $destino, $servicio, $t_serv, $turno, 'v');
              $tablaSalida = $datosVta[1];
              $datosvta = $datosVta[0];
              $chartIda = createFunctionChart("drawChartVta", $datosvta, "egr", "Salida de Planta","");
              $soloEntrada = false;
              $tg = htmlentities($tg);
           }
           else{
                $tg = $titulo;
           }

           $chartVta = createFunctionChart("drawChart", $datos, "ing", "");
           print "<script type=\"text/javascript\">
                          google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
                          google.setOnLoadCallback(drawChart);
                          google.setOnLoadCallback(drawChartVta);
                          $chartIda
                          $chartVta
          </script>";
        }
     }


?>
    <br><br>
         <form id="upuda" method="POST">
	           <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Graficas de Confiablidad y Eficiencia</legend>
		                 <div id="mensaje"> </div>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">Filtrar Datos</legend>
                         <table border="0" align="center" width="50%" name="tabla">
                                <tr>
                                    <td>Cliente</td>
                                    <td>
                                        <select id="clis" name="clis" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="10">TOYOTA</option>
                                        </select>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>Tipo</td>
                                    <td>
                                        <select id="tipo" name="tipo" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="2">PRODUCCION</option>
                                                <option value="1">ADMINISTRACION</option>
                                        </select>
                                    </td>
                                    <td>Turno</td>
                                    <td>
                                        <select id="turno" name="turno" class="ui-widget ui-widget-content  ui-corner-all">
                                                <option value="1"><?php print htmlentities('MAÑANA'); ?></option>
                                                <option value="2">TARDE</option>
                                                <option value="3">NOCHE</option>
                                        </select>
                                    </td>

                                    </tr>
                                    <tr>
                                        <td>Origen</td>
                                        <td>
                                            <select name="fcity_origen" id="fcity_origen" class="selec">

                                            <?php
                                                 $conn = conexcion();
                                                 $sql = "SELECT id, upper(ciudad)
                                                         FROM ciudades c
                                                         WHERE c.id in (select ciudades_id_destino from cronogramas where id_cliente = 10 group by ciudades_id_destino)
                                                         order by ciudad";
                                                 $result = mysql_query($sql, $conn);
                                                 $option = "";
                                                 while ($city = mysql_fetch_array($result)){
                                                       $option.="<option value='$city[0]'>$city[1]</option>";
                                                 }
                                                 print $option;
                                            ?>
                                            </select>
                                        </td>
                                        <td colspan='2'><input type="checkbox" name="city_origen"  id="city_origen" value="origen" class="city">Aplicar Filtro</td>
                                    </tr>
                                    <tr>
                                        <td>Destino</td>
                                        <td>
                                            <select id="fcity_destino" name="fcity_destino" class="selec">
                                            <?php
                                                 print $option;
                                            ?>
                                            </select>
                                        </td>
                                        <td colspan='2'><input type="checkbox" name="city_destino" id="city_destino" value="destino" class="city">Aplicar Filtro</td>
                                    </tr>
                                    <tr>
                                        <td>Servicios</td>
                                        <td>
                                        <div id="cronos">
                                        
                                        </div>
                                        </td>
                                        <td colspan='2'><input type="checkbox" id="servicios" name="servicios">Aplicar Filtro</td>
                                    </tr>
                                    <tr>
                                    <td>Desde</td>
                                    <td><input id="desde" name="desde"  type="text" size="20" value="<?php echo $_POST['desde'];?>"></td>
                                    <td>Hasta</td>
                                    <td><input id="hasta" name="hasta" type="text" size="20" value="<?php echo $_POST['hasta'];?>"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="4"></td>
                                    </tr>
                                    <tr>
                                    <td>Confiabilidad</td>
                                    <td><input id="cnf" type="radio" name="cnf" <?php echo $chcnf; ?> value="cnf"></td>
                                    <td>Eficiencia</td>
                                    <td><input id="efc" type="radio" name="cnf" value="efc" <?php echo $chefc; ?>></td>
                                    </tr>
                                    <tr>
                                    <td colspan="4" align="right">
                                        <input type="submit" value="Cargar Ordenes" id="cargar" name="cargar">
                                    </td>
                                </tr>
                         </table>
                         </fieldset>
                         <br>
                         <?php if (isset($_POST['cargar'])){ ?>
                         <h2><p align="center"><?php echo ($tg);?></p></h2> <br>
                         <div id="center"></h2>
                              <ul>
                                  <li><a href="#tabs-1">Grafico <?php if (!$soloEntrada) echo "(Entrada)";?></a></li>
                                  <li><a href="#tabs-2">Fuente de Datos</a></li>
                              </ul>
                              <div id="tabs-1" align="center">
                                   <?php echo $chart; ?>
                                  <div id="ing" style="width: 1200px; height: 400px;"></div>
                              </div>
                              <div id="tabs-2" align="center">
                                   <?php echo $tablaEntrada; ?>
                              </div>
                         </div>
                         <hr align="tr">
                         <?php  if (!$soloEntrada){   ?>
                         <div id="vuelta"></h2>
                              <ul>
                                  <li><a href="#tabs-3">Grafico (Salida)</a></li>
                                  <li><a href="#tabs-4">Fuente de Datos</a></li>
                              </ul>
                              <div id="tabs-3" align="center">
                                   <?php echo $chartv; ?>
                                   <div id="egr" style="width: 1200px; height: 400px;"></div>
                              </div>
                              <div id="tabs-4" align="center">
                                   <?php echo $tablaSalida; ?>
                              </div>
                         </div>
                         <?php }  ?>
                         <?php } ?>
            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type="hidden" name="order" id="order">
         </form>
         
         <?php
         
              print "<script>
                       $('.show').click(function(){
                       var par = $(this).attr('id');
                                            var dialog = $('<div style=\"display:none\" id=\"dialog\" class=\"loading\" align=\"center\"></div>').appendTo('body');
                                            dialog.dialog({
                                                           close: function(event, ui) {dialog.remove();},
                                                           title: 'Fuentes de grafico',
                                                           width:1050,
                                                           height:350,
                                                           modal:true,
                                                           show: {
                                                                 effect: 'blind',
                                                                 duration: 1000
                                                                 },
                                                           hide: {
                                                                 effect: 'blind',
                                                                 duration: 1000
                                                                 }
                                                           });
                                                           dialog.load('./fontdraw.php',{params:par},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass('loading');});
                       });
              </script>";
         
         ?>


</body>
</html>
