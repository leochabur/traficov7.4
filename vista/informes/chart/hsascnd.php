<?php ini_set("memory_limit","100M");// content="text/plain; charset=utf-8"
// Gantt hour example
require_once ('../../jpgraph/src/jpgraph.php');
require_once ('../../jpgraph/src/jpgraph_gantt.php');

$graph = new GanttGraph();
$graph->SetMarginColor('darkgreen@0.8');
$graph->SetColor('white');

// We want to display day, hour and minute scales
$graph->ShowHeaders(GANTT_HDAY | GANTT_HHOUR | GANTT_HMIN);

// We want to have the following titles in our columns
// describing each activity
//$graph->scale->actinfo->SetColTitles(
  //  array('Act','Duration','Start','Finish','Resp'));//,array(100,70,70,70));

// Uncomment the following line if you don't want the 3D look
// in the columns headers
//$graph->scale->actinfo->SetStyle(ACTINFO_2D);

//$graph->scale->actinfo->SetFont(FF_ARIAL,FS_NORMAL,30);

//These are the default values for use in the columns
//$graph->scale->actinfo->SetFontColor('black');
//$graph->scale->actinfo->SetBackgroundColor('lightgray');
//$graph->scale->actinfo->vgrid->SetStyle('solid');

$graph->scale->actinfo->vgrid->SetColor('gray');
$graph->scale->actinfo->SetColor('darkgray');

// Setup day format
$graph->scale->day->SetBackgroundColor('lightyellow:1.5');
//$graph->scale->day->SetFont(FF_ARIAL);
$graph->scale->day->SetStyle(DAYSTYLE_SHORTDAYDATE1);

// Setup hour format
$graph->scale->hour->SetIntervall(1);
$graph->scale->hour->SetBackgroundColor('lightyellow:1.5');
//$graph->scale->hour->SetFont(FF_FONT0);
$graph->scale->hour->SetStyle(HOURSTYLE_H24);
$graph->scale->hour->grid->SetColor('gray:0.8');

// Setup minute format
$graph->scale->minute->SetIntervall(15);
$graph->scale->minute->SetBackgroundColor('lightyellow:1.5');
//$graph->scale->minute->SetFont(FF_FONT0);
$graph->scale->minute->SetStyle(MINUTESTYLE_MM);
$graph->scale->minute->grid->SetColor('lightgray');

$graph->scale->tableTitle->Set('CONDUCTORES');
//$graph->scale->tableTitle->SetFont(FF_ARIAL,FS_NORMAL,12);
$graph->scale->SetTableTitleBackground('darkgreen@0.6');
$graph->scale->tableTitle->Show(true);

$graph->title->Set("ASIGNACION DE HORARIOS A CONDUCTORES DEL DIA");
$graph->title->SetColor('darkgray');
//$graph->title->SetFont(FF_VERDANA,FS_BOLD,14);

include('../../../controlador/ejecutar_sql.php');

// Some sample Gantt data
$sql="SELECT e.id_empleado, concat(apellido,', ',e.nombre), concat(fservicio, ' ', date_format(hcitacion, '%H:%i')) as sale, concat(fservicio, ' ',date_format(hfinservicio, '%H:%i')) as llega
FROM ordenes o
inner join empleados e on e.id_empleado = o.id_chofer_1
where fservicio = '2013-10-09' and o.id_estructura = 1  and not borrada and not suspendida
order by apellido";
$resu=ejecutarSQL($sql);
$data = array();
$i=0;
$j=0;
$row = mysql_fetch_array($resu);
      while ($row){
            $emp = $row[0];
            while (($row)&&($emp == $row[0])){
                  $data[$i++] = array($j,"$row[1]", "$row[2]","$row[3]");
                  $row = mysql_fetch_array($resu);
            }
            $j++;
    }


for($i=0; $i<count($data); ++$i) {
    $bar = new GanttBar($data[$i][0],$data[$i][1],$data[$i][2],$data[$i][3]);
    if( count($data[$i])>4 )
        $bar->title->SetFont($data[$i][4],$data[$i][5],$data[$i][6]);
    $bar->SetPattern(BAND_RDIAG,"yellow");
    $bar->SetFillColor("red");
    $graph->Add($bar);
}

$graph->Stroke();



?>
