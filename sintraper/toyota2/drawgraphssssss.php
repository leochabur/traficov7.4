<?php
     session_start();

     include( 'GoogChart.class.php' );
     include( 'bdadmin.php' );
     include( 'dateutils.php' );
     include('./main.php');
     define(RAIZ, '');
     define(STRUCTURED, $_SESSION['structure']);
?>

<?php
     encabezado('Menu Principal - Sistema de Administracion - Campana');

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
                                                       <?php if (isset($_POST['cargar'])){?>
                                                       
                                                             $("#tipo option[value='<?php echo $_POST['tipo'];?>']").attr('selected', 'selected');
                                                             $('#tipo, #turno, #clis').selectmenu({width: 350});
                                                       
                                                       <?php  } else {

                                                              print "\$('#turno option[value=$_POST[turno]]').attr('selected', 'selected');";
                                                              print "\$('#tipo option[value=$_POST[tipo]]').attr('selected', 'selected');";

                                                              print "\$('#tipo, #turno, #clis').selectmenu({width: 350});";

                                                       } ?>

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
           $conn = conexcion();
           $sql="SELECT date_format(o.fservicio, '%d'), round(sum(if (o.hllegada <= ho.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, ho.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, ho.hllegada))/60)-5)*0.06),
                               0))))/count(*)*100,2), date_format(o.fservicio, '%d/%m/%Y'), count(*), o.fservicio, day(o.fservicio), (month(o.fservicio)-1), year(o.fservicio)
                 FROM ordenes o
                 inner join horarios_ordenes ho on ho.id = o.id
                 inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 inner join (SELECT * from tiposervicio WHERE id = $t_serv) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join (SELECT * from turnos WHERE id = $turno) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 WHERE (o.fservicio between '$desde' and '$hasta') and (not o.borrada) and (not o.suspendida) and (o.id_cliente = 10) and (i_v = 'i')
                 group by fservicio, ts.id, tu.id, i_v";
         //  die($sql);
           $result = mysql_query($sql, $conn);

           $count = mysql_num_rows($result);
           $aux = 1;
           $tablaEntrada= "<table class='ui-widget ui-corner-all' width='50%'>
                           <thead class='ui-widget ui-widget-header'>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad Servicios</th>
                                    <th>% Confiabilidad</th>
                                </tr>
                           </thead>
                           <tbody>";
           $count = mysql_num_rows($result);
           $aux = 1;
           $j=0;
           $datos="[[{type: 'date', label: 'Fecha'}, 'Confiabilidad'],";
           while ($i = mysql_fetch_array($result)){
                 $datos.="[new Date($i[7], $i[6], $i[5]), $i[1]]";
                 if ($aux < $count)
                    $datos.=',';
                 $aux++;
                 
                 if (($j%2) == 0)
                    $color = '#D0D0D0';
                 else
                     $color = '#FFFFFF';
                 $data[$i[0]]=$i[1];
                 $auxid=$t_serv+'v';
                 $tablaEntrada.="<tr style='cursor: pointer;' class='show' bgcolor='$color' id=' $i[4] $i[4] $turno $t_serv".'i'."'>
                                 <td align='center'>$i[2]</td>
                                 <td align='right'>$i[3]</td>
                                 <td align='right'>$i[1]</td>
                                </tr>";
                 $j++;
           }
           $datos.="]";
           $tablaEntrada.="</tbody></table>";

	                            
           $sql="SELECT date_format(o.fservicio, '%d'), round((if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0))))*100,2), date_format(o.fservicio, '%d/%m/%Y'), count(*), o.fservicio, day(o.fservicio), (month(o.fservicio)-1), year(o.fservicio)
                 FROM ordenes o
                 inner join horarios_ordenes ho on ho.id = o.id
                 inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 inner join (SELECT * from tiposervicio WHERE id = $t_serv) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join (SELECT * from turnos WHERE id = $turno) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 WHERE (o.fservicio between '$desde' and '$hasta') and (not o.borrada) and (not o.suspendida) and (o.id_cliente = 10) and (i_v = 'v')
                 group by o.fservicio, ts.id, tu.id, i_v";

           $result = mysql_query($sql, $conn);

           $tablaSalida= "<table class='ui-widget ui-corner-all' width='50%'>
                           <thead class='ui-widget ui-widget-header'>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad Servicios</th>
                                    <th>% Confiabilidad</th>
                                </tr>
                           </thead>
                           <tbody>";
           $count = mysql_num_rows($result);
           $aux = 1;
           $j=1;
           $datosvta="[[{type: 'date', label: 'Fecha'}, 'Confiabilidad'],";
           while ($i = mysql_fetch_array($result)){
                 $datosvta.="[new Date($i[7], $i[6], $i[5]), $i[1]]";
                 if ($aux < $count)
                    $datosvta.=',';
                 $aux++;
                 if (($j%2) == 0)
                    $color = '#D0D0D0';
                 else
                     $color = '#FFFFFF';
                 $tablaSalida.="<tr class='show' style='cursor: pointer;' bgcolor='$color' id=' $i[4] $i[4] $turno $t_serv".'v'."'>
                                 <td align='center'>$i[2]</td>
                                 <td align='right'>$i[3]</td>
                                 <td align='right'>$i[1]</td>
                                </tr>";
                 $j++;
           }
           $datosvta.="]";
           $tablaSalida.="</tbody></table>";
           print "<script type=\"text/javascript\">
                          google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
                          google.setOnLoadCallback(drawChart);
                          google.setOnLoadCallback(drawChartVta);
                          function drawChartVta() {
                                   var datavta = google.visualization.arrayToDataTable($datosvta);
                                   var options = {
                                               pointSize: 8,
                                               title: 'Salida de Planta',
                                               hAxis: {
                                               format: 'd',
                                                       gridlines: {count: 15}
                                               },
                                               vAxis: {
                                                      gridlines: {count:10},
                                                      viewWindow: {
                                                                  max:100,
                                                                   min:50
                                                                   }
                                               }
                                               };
                                   var chart = new google.visualization.LineChart(document.getElementById('egr'));
                                   chart.draw(datavta, options);
                          }

                          function drawChart() {
                                   var data = google.visualization.arrayToDataTable($datos);
                                   var options = {
                                               pointSize: 8,
                                               title: 'Entrada a Planta',
                                               hAxis: {
                                               format: 'd',
                                                       gridlines: {count: 15}
                                               },
                                               vAxis: {
                                                      gridlines: {count:10},
                                                      viewWindow: {
                                                                  max:100,
                                                                   min:50
                                                                   }
                                               }
                                               };
                                   var chart = new google.visualization.LineChart(document.getElementById('ing'));
                                   chart.draw(data, options);
                          }
          </script>";
        }
        else{

           $title = "Graficos de Eficiencia";
           $chcnf = "";
           $chefc = "checked";
           $conn = mysql_connect("190.220.252.243", "mbexpuser", "Mb2013Exp");
           mysql_select_db('mbexport', $conn);
           $sql="SELECT date_format(fservicio, '%d'), round((sum(cantpax)/sum(cantasientos))*100,2) as por, date_format(fservicio, '%d/%m/%Y'), count(*), fservicio
                 FROM ordenes o
                 inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 inner join (SELECT * from tiposervicio WHERE id = $t_serv) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join (SELECT * from turnos WHERE id = $turno) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 inner join unidades i on i.id = o.id_micro
                 WHERE (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = 'i')
                 group by fservicio, ts.id, tu.id, i_v";

          /// print($sql."<br>");
           $result = mysql_query($sql, $conn);
      //    die("ref ".mysql_num_rows($result));
           
           $chart = new GoogChart();
           $data = array();
           $tablaEntrada= "<table class='ui-widget ui-corner-all' width='50%'>
                           <thead class='ui-widget ui-widget-header'>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad Servicios</th>
                                    <th>% Eficiencia</th>
                                </tr>
                           </thead>
                           <tbody>";
           $j=1;
           while ($i = mysql_fetch_array($result)){
                 if (($j%2) == 0)
                    $color = '#D0D0D0';
                 else
                     $color = '#FFFFFF';
                    $data[$i[0]]=$i[1];
                    $tablaEntrada.="<tr class='show' style='cursor: pointer;' bgcolor='$color' id=' $i[4] $i[4] $turno $t_serv".'i'."'>
                                 <td align='center'>$i[2]</td>
                                 <td align='right'>$i[3]</td>
                                 <td align='right'>$i[1]</td>
                             </tr>";
                    $j++;
           }
           $tablaEntrada.="</tbody></table>";
           $color = array(
                          '#99C754',
                          '#54C7C5',
                          '#999999',
           );
           $chart->setChartAttrs( array(
	                                 'type' => 'line',
	                                 'title' => 'Entrada',
	                                 'data' => $data,
	                                 'size' => array( 1000, 300 ),
	                                 'color' => $color,
	                                 'labelsXY' => true
	                            ));

           $sql="SELECT date_format(fservicio, '%d'), round((sum(cantpax)/sum(cantasientos))*100,2) as por, date_format(fservicio, '%d/%m/%Y'), count(*), fservicio
                 FROM ordenes o
                 inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 inner join (SELECT * from tiposervicio WHERE id = $t_serv) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join (SELECT * from turnos WHERE id = $turno) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 inner join unidades i on i.id = o.id_micro
                 WHERE (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = 'v')
                 group by fservicio, ts.id, tu.id, i_v";
           $result = mysql_query($sql, $conn);
           $chartv = new GoogChart();
           $data2 = array();
           $tablaSalida= "<table class='ui-widget ui-corner-all' width='50%'>
                           <thead class='ui-widget ui-widget-header'>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad Servicios</th>
                                    <th>% Eficiencia</th>
                                </tr>
                           </thead>
                           <tbody>";
           while ($i = mysql_fetch_array($result)){
                 if (($j%2) == 0)
                    $color = '#D0D0D0';
                 else
                     $color = '#FFFFFF';
                    $data2[$i[0]]=$i[1];
                    $tablaSalida.="<tr class='show' style='cursor: pointer;' bgcolor='$color' id=' $i[4] $i[4] $turno $t_serv".'v'."'>
                                 <td align='center'>$i[2]</td>
                                 <td align='right'>$i[3]</td>
                                 <td align='right'>$i[1]</td>
                             </tr>";
                    $j++;
           }
           $tablaSalida.="</tbody></table>";
           $color = array(
                          '#FF0000',
                          '#54C7C5',
                          '#999999',
           );
           $chartv->setChartAttrs( array(
	                                 'type' => 'line',
	                                 'title' => 'Salida',
	                                 'data' => $data2,
	                                 'size' => array( 1000, 300 ),
	                                 'color' => $color,
	                                 'labelsXY' => true
	                            ));
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
                                    <td><input id="cnf" type="radio" name="cnf" <?php echo $chcnf;?> value="cnf"></td>
                                    <td>Eficiencia</td>
                                    <td><input id="efc" type="radio" name="cnf" value="efc" <?php echo $chefc;?>></td>
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
                         <h2><p align="center"><?php echo htmlentities($tg); ?></p></h2> <br>
                         <div id="center"></h2>
                              <ul>
                                  <li><a href="#tabs-1">Grafico (Entrada)</a></li>
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
                         <?}?>
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

