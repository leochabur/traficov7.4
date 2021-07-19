
<?php
/** Include class */
include( 'GoogChart.class.php' );

/** Create chart */
$chart = new GoogChart();

$sql="SELECT date_format(fservicio, '%d'), round(sum(if (o.hllegada <= s.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, s.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, s.hllegada))/60)-5)*0.06),
                               0))))/count(*)*100,2)
        FROM ordenes o
        inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
        inner join (SELECT * from tiposervicio WHERE id = 1) ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
        inner join (SELECT * from turnos WHERE id = 1) tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
        WHERE (fservicio between '2015-06-01' and '2015-06-15') and (not borrada) and (not suspendida) and (o.id_cliente = 10) and (i_v = 'v')
        group by fservicio, ts.id, tu.id, i_v";
        
$conn = mysql_connect('190.220.252.243', 'mbexpuser', 'Mb2013Exp');
mysql_select_db('mbexport', $conn);

$result = mysql_query($sql, $conn);

/*$data = array(
			'1' => 22,
			'2' => 30.7,
			'3' => 1.7,
			'4' => 36.5,
			'5' => 1.1,
			'6' => 2,
			'7' => 1.4,
		);   */

$data = array();
while ($i = mysql_fetch_array($result)){
        $data[$i[0]]=$i[1];

}
		
echo '<h2>Pie chart</h2>';
$chart->setChartAttrs( array(
	'type' => 'line',
	'title' => 'Browser market 2008',
	'data' => $data,
	'size' => array( 700, 300 ),
	'color' => "#000000",
	'labelsXY' => true
	));
// Print chart
echo $chart;


?>

