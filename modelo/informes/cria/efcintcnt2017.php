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
     $sql = "select interno, nombre,count(*),  round(avg(conf), 2) as conf, round(sum(cantpax)/sum(cantasientos), 2)*100 as efi, razon_social, sum(km)
from(
select upper(o.nombre) as nombre, if (i_v = 'v',cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2)), cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0))) as decimal(5,2)))*100 as conf, i_v, interno, o.id_servicio, cantpax, cantasientos, upper(razon_social) as razon_social, cli.id as id_cli, o.km
from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
inner join clientes cli on cli.id = c.id_cliente
inner join unidades u on u.id = o.id_micro
where fservicio between '$desde' and '$hasta' and o.id_estructura = $_SESSION[structure] and u.id_propietario = $_POST[emp] and not borrada and not suspendida) o
group by interno, id_cli, id_servicio
order by interno, razon_social, nombre";
     $conn = conexcion();
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>

                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $int = $data['interno'];
           $tot = 0;
           $i = 0;
           $tabla.='<tr class="ui-widget-header"><td colspan="7" align="center">INTERNO '.$int.'</td></tr>
                    <tr class="ui-widget-header">
                        <th>Cliente</th>
                        <th>Nombre Servicio</th>
                        <th>Cantidad Servicio</th>
                        <th>Confiabilidad Servicio</th>
                        <th>Km</th>
                    </tr>';
           while (($data) &&($int == $data['interno'])){
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='left'>$data[5]</td>
                            <td align='left'>$data[1]</td>
                            <td align='right'>$data[2]</td>
                            <td align='right'>$data[3]%</td>
                            <td align='right'>$data[6]</td>
                            </tr>";
               $tot=$tot + $data[6];
               $data = mysql_fetch_array($result);
               $i++;
           }
           $tabla.="<tr><td colspan='7'><hr align='tr'></td></tr>";
     }
     $tabla.='</tbody>
              </table>
                  <style>
                         #example tbody tr:hover {background-color: #FF8080;}
                         #example { font-size: 85%; }
                  </style>';
    print $tabla;
  }
  
?>

