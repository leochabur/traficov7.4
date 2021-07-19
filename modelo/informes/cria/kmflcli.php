<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $interno = '';
     if ($_POST['internos']){
           $interno = "and (o.id_micro = $_POST[internos])";
     }
     if (false){//$_POST['emp'] == 10){
     $sql = "select date_format(o.fservicio, '%d/%m/%Y') as fservicio, date_format(o.hcitacion, '%H:%i') as hsalida, date_format(o.hfinservicio, '%H:%i') as hfin,
             upper(o.nombre) as nombre,  upper(razon_social) as cli, interno, o.km, razon_social, hcitacion
             from (select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, vacio
                   from ordenes ord
                   where (ord.fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida)
             ) o
             left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
             inner join unidades u on (u.id = o.id_micro) and (u.id in (26, 139))
             inner join empleados e on e.id_empleado = o.id_chofer_1
             where not vacio
             union all
             select date_format(o.fservicio, '%d/%m/%Y') as fservicio, date_format(o.hcitacion, '%H:%i') as hsalida, date_format(o.hfinservicio, '%H:%i') as hfin,
             upper(o.nombre) as nombre,  upper(razon_social) as cli, interno, o.km, razon_social, hcitacion
             from (select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, vacio
                   from ordenes ord
                   where (ord.fservicio between '$desde' and '$hasta') and (ord.fservicio >= '2013-11-18') and (id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida)
             ) o
             left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
             inner join unidades u on (u.id = o.id_micro) and (u.id in (167))
             inner join empleados e on e.id_empleado = o.id_chofer_1
             where not vacio
             order by interno, fservicio, razon_social, hcitacion";

     }
     else{
     $sql = "select concat(apellido,', ', nombre) as chofer, upper(razon_social) as cliente, sum(time_to_sec(hs)) as hs, conductor, sum(km)
from(
select km, id_chofer_1 as conductor, id_cliente, id_estructura_cliente, if(hfinservicio > hcitacion, timediff(hfinservicioreal, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicioreal, '00:00:00'))) as hs
from ordenes
where fservicio between '$desde' and '$hasta' and id_estructura = $_SESSION[structure] and not borrada and not suspendida
      and id_chofer_1 is not null
union all
select km, id_chofer_2 as conductor, id_cliente, id_estructura_cliente, if(hfinservicio > hcitacion, timediff(hfinservicioreal, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicioreal, '00:00:00'))) as hs
from ordenes
where fservicio between '$desde' and '$hasta' and id_estructura = $_SESSION[structure] and not borrada and not suspendida
      and id_chofer_2 is not null
) o
inner join empleados e on e.id_empleado = o.conductor
inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
where id_empleador = $_POST[emp]
group by conductor, id_cliente
order by apellido, cliente";
     }
     $conn = conexcion();
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>

                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $chofer = $data['chofer'];
           $id_cond = $data['conductor'];
           $tot_km = 0;
           $tot_hs = 0;
           $i = 0;
           $tabla.='<tr class="ui-widget-header"><td colspan="7" align="center">'.$chofer.'</td></tr>
                    <tr class="ui-widget-header">
                        <th>Razon Social</th>
                        <th>Km</th>
                        <th>Horas conduccion</th>
                    </tr>';
           while (($data) &&($id_cond == $data['conductor'])){
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='left'>$data[1]</td>
                            <td align='right'>$data[4]</td>
                            <td align='right'>".conversorSegundosHoras($data[2])."</td>
                            </tr>";
               $tot_km = $tot_km + $data[4];
               $tot_hs = $tot_hs + $data[2];
               $data = mysql_fetch_array($result);
               $i++;
           }
           $tabla.="<tr><td>TOTAL</td><td align='right'>$tot_km</td><td align='right'>".conversorSegundosHoras($tot_hs)."</td></tr>
                    <tr><td colspan='3'><hr align='tr'></td></tr>";
     }
     $tabla.='</tbody>
              </table>
                  <style>
                         #example tbody tr:hover {background-color: #FF8080;}
                         #example { font-size: 85%; }
                  </style>';
    print $tabla;
  }
  
  function conversorSegundosHoras($tiempo_en_segundos) {
	$horas = floor($tiempo_en_segundos / 3600);
	$minutos = floor(($tiempo_en_segundos - ($horas * 3600)) / 60);
	$segundos = $tiempo_en_segundos - ($horas * 3600) - ($minutos * 60);
    if ($minutos < 10)
       $minutos="0$minutos";

	return $horas . ':' . $minutos;
}
  
?>

