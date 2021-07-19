<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include ("./js/jpgraph-1.27.1/src/jpgraph.php");
  include ("./js/jpgraph-1.27.1/src/jpgraph_line.php");
  include('./ejecutar_sql.php');
  include('./dateutils.php');

  if ($_GET['i_v'] == 'i'){
  $sql="SELECT fservicio, round(sum(if (o.hllegada <= s.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, s.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, s.hllegada))/60)-5)*0.06),
                               0))))/count(*)*100,2)
        FROM ordenes o
        inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
        inner join (SELECT * from tiposervicio WHERE id = $_GET[ts]) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
        inner join (SELECT * from turnos WHERE id = $_GET[tu]) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
        WHERE (fservicio between'$_GET[desde]' and '$_GET[hasta]') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = '$_GET[i_v]')
        group by fservicio, ts.id, tu.id, i_v";
  }
  else{
  $sql="SELECT fservicio, round((if (o.hsalida <= s.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, s.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, s.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, s.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, s.hsalida))/60)-5)*0.06),
                               0))))*100,2)
        FROM ordenes o
        inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
        inner join (SELECT * from tiposervicio WHERE id = $_GET[ts]) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
        inner join (SELECT * from turnos WHERE id = $_GET[tu]) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
        WHERE (fservicio between'$_GET[desde]' and '$_GET[hasta]') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = '$_GET[i_v]')
        group by fservicio, ts.id, tu.id, i_v";

  
  }

  $result = ejecutarSQL($sql);
  $datax = array();
  $datay = array();
  while ($data = mysql_fetch_array($result)){
        array_push($datax, $data[0]);
        array_push($datay, $data[1]);
  }

  // Setup graph
  $graph = new Graph(600,250,"auto");
  $graph->img->SetMargin(50,10,10,80);
  $graph->SetScale("textlin");
  $graph->SetShadow();
  //Setup title
  $graph->title->Set("$_GET[title]");

  // Use built in font
  $graph->title->SetFont(FF_COMIC,FS_NORMAL,11);

  // Slightly adjust the legend from it's default position
  $graph->legend->Pos(0.03,0.5,"right","center");
  $graph->legend->SetFont(FF_FONT1,FS_BOLD);

  // Setup X-scale
  $graph->xaxis->SetTickLabels($datax);
  $graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
  $graph->xaxis->SetLabelAngle(90);

  // Create the first line
  $p1 = new LinePlot($datay);
  $p1->mark->SetType(MARK_FILLEDCIRCLE);
  $p1->mark->SetFillColor("red");
  $p1->mark->SetWidth(4);
  $p1->SetColor("blue");
  $p1->SetCenter();
  $graph->Add($p1);

  // Output line
  $graph->Stroke();

  
?>

