<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
require('linegraph.php');
require('controlador/bdadmin.php');
require('modelo/utils/utils.php');


$sql="select day(fservicio) as dia, SUBSTRING(ts.tipo,1,4) as tipo, turno, round(avg(confiabilidad),2) as conf, round(avg(eficiencia),2) as efic
from (
SELECT o.fservicio, round((o.cantpax/cantasientos)*100,2) as Eficiencia,
       if (o.id_servicio is null, (select id_tipo_servicio from tipotnoordenes where id_orden = o.id), s.id_tipoServicio) as tipo,
       turno, o.id_servicio, o.cantpax,

       if (i_v is null,
                     if(o.id_ciudad_destino = 3,
cast(if (o.hllegada <= ho.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, ho.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, ho.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                     ,
cast(if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                   ), if (i_v = 'i',
cast(if (o.hllegada <= ho.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, ho.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, ho.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                                                 ,
  cast(if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2)))) as Confiabilidad, o.km, o.id_micro as id_micro

FROM (select o.id, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_estructura_ciudad_destino, fservicio, cantpax, id_servicio,
             id_ciudad_destino, o.hllegada, o.hsalida, km, id_micro,
             if (id_servicio is null, (select id_tipo_servicio from tipotnoordenes where id_orden = o.id), s.id_tipoServicio) as tipo
      from ordenes o
      left join servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
      where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_SESSION[structure] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida) o
inner join (select * from horarios_ordenes where month(fservicio) = $_GET[mes] and year(fservicio) = $_GET[anio] and id_estructura = $_SESSION[structure] and id_cliente = $_GET[cli]) ho on ho.id = o.id
inner JOIN servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
left join cronogramas c on c.id = s.id_cronograma and s.id_estructura_cronograma = c.id_estructura
left JOIN turnos tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
left JOIN ciudades ori ON ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
left join unidades u on u.id = o.id_micro
) o
left JOIN tiposervicio ts ON ts.id = o.tipo
where o.tipo not in (3,16)
group by day(fservicio), o.tipo, turno";
//die($sql);

$export = conexcion();//mysql_connect('200.80.61.27', 'mbexpuser', 'Mb2013Exp');
//mysql_select_db('mbexport', $export);

$pdf = new PDF_LineGraph();
$pdf->DefOrientation = 'L';
$pdf->setTitle("Graficos de confiabilidad correspondiente a ".ucwords(nombremes($_GET['mes']))." de ".$_GET['anio']);

$datos=array();
$result = mysql_query($sql, $export);
while ($data = mysql_fetch_array($result)){
      if (!isset($datos[$data['tipo']])){
         $datos[$data['tipo']]=array();
      }
      if (!isset($datos[$data['tipo']][$data['turno']])){
         $datos[$data['tipo']][$data['turno']]=array();
      }
      $datos[$data['tipo']][$data['turno']][$data['dia']]=$data[3];
}

foreach ($datos as $clave=>$valor){
    //    print "$clave<br>";
        foreach ($valor as $cltur=>$valtur){
                $pdf->AddPage();
               // $pdf->Image('masterbus-log.png',10,8,33);
               // $pdf->ln();
            //    $pdf->Cell(15,6,"Graficos de confiabilidad $clave. turno $cltur",0,1,'L');
             //   $pdf->ln();
                $pdf->LineGraph(280,100,array("$clave-$cltur"=>$valtur),'VHkBvBgBdB', null, 100);

           /*     print "$cltur<br>";
                print_r($valtur);
                print "<br>";  */
        }
}

//print_r($datos);
//die();


/*$data = /*array(
	'Conf.' => array(
		'1' => 92.7,
		'2' => 90.0,
		'3' => 91.3928571,
		'4' => 92.2903226,
		'5' => 98.1,
 		'6' => 92.7,
		'7' => 90.0,
		'8' => 91.3928571,
		'9' => 92.2903226,
		'10' => 98.1,
 		'11' => 92.7,
		'12' => 90.0,
		'13' => 91.3928571,
		'14' => 92.2903226,
		'15' => 98.1,
 		'16' => 92.7,
		'17' => 90.0,
		'18' => 91.3928571,
		'19' => 92.2903226,
		'20' => 98.1,
 		'21' => 92.7,
		'22' => 90.0,
		'23' => 91.3928571,
		'24' => 92.2903226
	)
);     */
/*$colors = array(
	'Conf.' => array(114,171,237)
);       */

/*$pdf->AddPage();
// Display options: all (horizontal and vertical lines, 4 bounding boxes)
// Colors: fixed
// Max ordinate: 6
// Number of divisions: 3
$pdf->LineGraph(190,100,$data,'VHkBvBgBdB',$colors,6,3);

$pdf->AddPage();
// Display options: horizontal lines, bounding box around the abscissa values
// Colors: random
// Max ordinate: auto
// Number of divisions: default
$pdf->LineGraph(190,100,$data,'HvB');
                 */
/*$pdf->AddPage();
$pdf->Cell(15,6,'Graficos de confiabilidad',0,1,'L');
$pdf->ln();
// Display options: vertical lines, bounding box around the legend
// Colors: random
// Max ordinate: auto
// Number of divisions: default
$pdf->LineGraph(280,100,$data,'VHkBvBgBdB', null, 100);    */

//$pdf->AddPage();
// Display options: horizontal lines, bounding boxes around the plotting area and the entire area
// Colors: random
// Max ordinate: 20
// Number of divisions: 10
//$pdf->LineGraph(190,100,$data,'HgBdB',null,20,10);

$pdf->Output();
?>
