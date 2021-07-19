<?php
include ("../../../controlador/ejecutar_sql.php");
include ("../../js/jpgraph-1.27.1/src/jpgraph.php");
include ("../../js/jpgraph-1.27.1/src/jpgraph_gantt.php");
// A new graph with automatic size
$fecha = explode('/', $_GET['desde']);
$fecha = "$fecha[2]-$fecha[1]-$fecha[0]";


$result = ejecutarSQL("SELECT o.id, interno, date_format(hsalida, '%H:%i') as hsalida,
                              if (hllegada < hsalida, '23:59', date_format(hllegada, '%H:%i')) as hllegada
                        FROM ordenes o
                        inner join unidades u on u.id = o.id_micro
                        where fservicio = '$fecha' and o.id_estructura = $_SESSION[structure]   and u.id_propietario = 1
                        order by interno, hsalida");

$graph = new GanttGraph();
$graph->SetMarginColor('blue:1.7');
$graph->SetColor('white');

$graph->SetBackgroundGradient('navy','white',GRAD_HOR,BGRAD_MARGIN);
$graph->scale->hour->SetBackgroundColor('lightyellow:1.5');
//$graph->scale->hour->SetFont(FF_FONT1);
$graph->scale->day->SetBackgroundColor('lightyellow:1.5');
//$graph->scale->day->SetFont(FF_FONT1,FS_BOLD);

$graph->title->Set("Diagrama Unidades correspondiente al $_GET[desde]");
$graph->title->SetColor('white');
//$graph->title->SetFont(FF_VERDANA,FS_BOLD,14);

$graph->ShowHeaders(GANTT_HHOUR);

$graph->scale->week->SetStyle(WEEKSTYLE_FIRSTDAY);
//$graph->scale->week->SetFont(FF_FONT1);
$graph->scale->hour->SetIntervall(0.5);

$graph->scale->hour->SetStyle(HOURSTYLE_HM24);
$graph->scale->day->SetStyle(DAYSTYLE_SHORTDAYDATE3);
$graph->hgrid-> Show();
$graph->hgrid-> line->SetColor('lightblue' );
$graph->hgrid-> SetRowFillColor( 'darkblue@0.9');
$data = mysql_fetch_array($result);
$i=0;
while ($data){
      $interno = $data[1];
      while ($interno == $data[1]){
            $activity = new GanttBar($i,"$interno","$data[2]","$data[3]");
            $graph->Add($activity);
            $data = mysql_fetch_array($result);
      }
      $i++;
}

$graph->Stroke();
?>


