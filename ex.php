<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
require('linegraph.php');
require('controlador/bdadmin.php');
require('modelo/utils/utils.php');


$sql="select day(fservicio) as dia, SUBSTRING(tipo,1,4) as tipo, turno, round(avg(confiabilidad),2) as conf, round(avg(eficiencia),2) as efic
from (
      SELECT date_format(o.fservicio, '%d/%m/%Y') as fecha, round((o.cantpax/cantasientos)*100,2) as Eficiencia, o.cantpax,
      if (i_v = 'i',
cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))
                                                 ,
  cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0)))*100 as decimal(5,2))) as Confiabilidad, o.km, o.id_micro as id_micro, ts.id as tipo_serv, tipo, turno, fservicio

      FROM (select o.id, id_estructura_servicio, hsalidaplantareal, hllegadaplantareal, id_ciudad_origen, id_estructura_ciudad_origen,
                   id_estructura_ciudad_destino, fservicio, cantpax, id_servicio,
                   id_ciudad_destino, o.hllegada, o.hsalida, km, id_micro,
                   if (tto.id_turno is null, s.id_turno, tto.id_turno) as id_turno,
                   if (tto.id_tipo_servicio is null, s.id_TipoServicio, tto.id_tipo_servicio) as id_tipo_servicio,
                   if (tto.i_v is null, s.i_v, tto.i_v) as i_v
            from ordenes o
            left join (select * from servicios where id_estructura = $_GET[str]) s on s.id = o.id_servicio
            left join tipotnoordenes tto on tto.id_orden = o.id
            where month(o.fservicio) = $_GET[mes] and year(o.fservicio) = $_GET[anio] and o.id_estructura = $_GET[str] and o.id_cliente = $_GET[cli] and not o.borrada and not o.suspendida and (hllegadaplantareal is not null || hsalidaplantareal is not null)) o
      inner JOIN (select * from tiposervicio where id_estructura = $_GET[str]) ts ON ts.id = id_tipo_servicio
      inner JOIN (select * from turnos where id_estructura = $_GET[str]) tu ON tu.id = id_turno
      left JOIN ciudades ori ON ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
      left JOIN ciudades des ON des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
      left join unidades u on u.id = o.id_micro
) o
where tipo_serv not in (16, 3)
group by day(fservicio), tipo, turno";
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
      if (!isset($datos[$data['tipo']][ utf8_decode($data['turno'])])){
         $datos[$data['tipo']][ utf8_decode( $data['turno'])]=array();
      }
      $datos[$data['tipo']][ utf8_decode( $data['turno'])][$data['dia']]=$data[3];
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
