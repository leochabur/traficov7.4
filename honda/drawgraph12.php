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

   <link type="text/css" href="./css/jquery.ui.selectmenu.css" rel="stylesheet" />
  <link type="text/css" href="./css/demo_page.css" rel="stylesheet" />
    <link type="text/css" href="./css/demo_table.css" rel="stylesheet" />
  <link type="text/css" href="./css/jquery.dataTables.css" rel="stylesheet" />
    
<script type="text/javascript" src="./js/validate-form/jquery.validate.min.js"></script>
<script type="text/javascript" src="./js/validate-form/jquery.metadata.js"></script>
 <script type="text/javascript" src="./js/jquery.maskedinput-1.3.js"></script>
   <script type="text/javascript" src="./js/jquery.ui.selectmenu.js"></script>
    <script type="text/javascript" src="./js/jquery.ui.datepicker-es.js"></script>

  <script type="text/javascript" src="./js/jquery.dataTables.min.js"></script>
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
                                                       $('#tipo, #turno, #clis').selectmenu({width: 350});
                                                       $('#cargar').button();

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
           $tg.=" TURNO MA�ANA";
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
           $sql="SELECT date_format(fservicio, '%d'), round(sum(if (o.hllegada <= s.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, s.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, s.hllegada))/60)-5)*0.06),
                               0))))/count(*)*100,2), day(fservicio), month(fservicio), year(fservicio)
                 FROM ordenes o
                 inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 inner join (SELECT * from tiposervicio WHERE id = $t_serv) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join (SELECT * from turnos WHERE id = $turno) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 WHERE (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = 'i')
                 group by fservicio, ts.id, tu.id, i_v";
           $result = mysql_query($sql, $conn);
           $datos="[[{type: 'date', label: 'Eficiencia'}, 'Eficiencia'],";
           $count = mysql_num_rows($result);
           $aux = 1;
           while ($data = mysql_fetch_array($result)){
                 $datos.="[new Date($data[4], $data[3], $data[2]), $data[1]]";
                 if ($aux < $count)
                    $datos.=',';
                 $aux++;
           }
           $datos.="]";
           
           print "<script type=\"text/javascript\">
                          google.load(\"visualization\", \"1\", {packages:[\"corechart\"]});
                          google.setOnLoadCallback(drawChart);
                          function drawChart() {
                                   var data = google.visualization.arrayToDataTable($datos);
                                   var options = {
                                               title: 'Balance de la Compania',
                                               hAxis: {
                                               format: 'd',
                                                       gridlines: {count: 15}
                                               },
                                               vAxis: {
                                                      gridlines: {color: 'none'},
                                                      minValue: 0
                                               }
                                               };
                                   var chart = new google.visualization.LineChart(document.getElementById('ida'));
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
           $sql="SELECT date_format(fservicio, '%d'), round((sum(cantpax)/sum(cantasientos))*100,2) as por
                 FROM ordenes o
                 inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 inner join (SELECT * from tiposervicio WHERE id = $t_serv) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 inner join (SELECT * from turnos WHERE id = $turno) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 inner join unidades i on i.id = o.id_micro
                 WHERE (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = 'i')
                 group by fservicio, ts.id, tu.id, i_v";

           $result = mysql_query($sql, $conn);
           
           $chart = new GoogChart();
           $data = array();
           while ($i = mysql_fetch_array($result)){
                    $data[$i[0]]=$i[1];
           }
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
                                                <option value="1"><?php print htmlentities('MA�ANA'); ?></option>
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
                         <div align="center"><h2><p align="center"><?php echo $title."<br><hr><br>".htmlentities($tg);?></p></h2></div>
                         <div id="ida" style="width: 900px; height: 500px;"></div>
                         <hr align="tr">
                         <div id="vuelta" align="center"><?php echo $chartv;?><br><?php if (isset($_POST['cargar'])){?><input type="button" id="<?php echo " $desde $hasta $turno $t_serv"."v";?>" value="Ver Datos"><?php }?></div>

            </fieldset>
            <input type="hidden" name="accion" id="accion" value="list">
            <input type="hidden" name="order" id="order">
         </form>
         
         <?php
         
              print "<script>
                       $(':button').button().click(function(){

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

