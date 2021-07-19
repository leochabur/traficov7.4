<?php
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
     $sql = "SELECT date_format(fservicio,'%d/%m/%Y') as fecha,
                    date_format(hfinservicio,'%H:%i:%s') as hfin,
                    nombre,
                    upper(c.razon_social) as cli,
                    interno,
                    upper(apenom),
                    date_format(fecha_accion, '%d/%m/%Y  -  %H:%i:%s') as modificada,
                    o.id, fecha_accion, 1  as sino
             FROM ordenes o
             inner join clientes c on c.id = o.id_cliente
             left join unidades u on u.id = o.id_micro
             inner join usuarios us on us.id = o.id_user
             where (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_POST[str]) and (finalizada)
union all
SELECT date_format(fservicio,'%d/%m/%Y') as fecha,
                    date_format(hfinservicio,'%H:%i:%s') as hfin,
                    nombre,
                    upper(c.razon_social) as cli,
                    interno,
                    upper(apenom),
                    date_format(fecha_accion, '%d/%m/%Y  -  %H:%i:%s') as modificada,
                    o.id, fecha_accion, 0  as sino
             FROM ordenes_modificadas o
             inner join clientes c on c.id = o.id_cliente
             left join unidades u on u.id = o.id_micro
             inner join usuarios us on us.id = o.id_user
             where (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_POST[str])
order by id, fecha_accion desc
";/*SELECT date_format(fservicio,'%d/%m/%Y') as fecha,
                    date_format(hfinservicio,'%H:%i:%s') as hfin,
                    nombre,
                    upper(c.razon_social) as cli,
                    interno,
                    upper(apenom),
                    date_format(fecha_accion, '%d/%m/%Y  -  %H:%i:%s') as modificada,
                    (SELECT hfinservicio FROM ordenes_modificadas where id = o.id order by fecha_accion asc limit 0,1)
             FROM ordenes o
             inner join clientes c on c.id = o.id_cliente
             left join unidades u on u.id = o.id_micro
             inner join usuarios us on us.id = o.id_user
             where (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_POST[str]) and (finalizada)
             order by fservicio, hfinservicio"; */

     $conn = conexcion();

     
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Fecha</th>
                        <th>Servicio</th>
                        <th>Cliente</th>
                        <th>Interno</th>
                         <th>H. Fin x Diagrama</th>
                         <th>H. Fin Real</th>
                        <th>Usuario Cierre</th>
                        <th>Fecha - Hora Cierre</th>
                    </tr>
                    </thead>
                    <tbody>';
     $i=0;
     $data = mysql_fetch_array($result);
     while ($data){
           $sino=0;
           if ($data[sino]){
              $hfinservcierre = $data[1];
              $horacierre = $data[6];
              $usercierre = $data[5];
              $sino = $data[sino];
           }
           $orden = $data[7];
           while ($orden == $data[7]){
                 $ulthora = $data[1];
                 $data = mysql_fetch_array($result);
           }
           if ($sino){
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='left'>$data[0]</td>
                            <td align='left'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='right'>$data[4]</td>
                            <td align='right'>$ulthora</td>
                            <td align='right'>$hfinservcierre</td>
                            <td align='left'>$usercierre</td>
                            <td align='right'>$horacierre</td>
                            </tr>";
               $i++;
           }
     }
     $tabla.='</tbody>
              </table>
                  <style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {

                                        background-color: #FF8080;
}
                  </style>';
    print $tabla;
  }
  
?>

