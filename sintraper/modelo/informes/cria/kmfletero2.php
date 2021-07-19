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
     
     $conn = conexcion();
     
     $sql_int = "select u.id, interno
                 from ordenes o
                 inner join unidades u on u.id = o.id_micro and u.id_propietario = $_POST[emp]
                 where o.id_estructura = $_SESSION[structure] and fservicio between '$desde' and '$hasta'
                 group by id_micro";
     $resu_int = mysql_query($sql_int, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>

                    </thead>
                    <tbody>';
     while ($data_int = mysql_fetch_array($resu_int)){
           $sql_int_prop = "SELECT propietario_desde
                            FROM propietariounidad p
                            where id_unidad = $data_int[0] and id_propietario = $_POST[emp] and propietario_desde between '$desde 00:00:00' and '$hasta 23:59:59'";
           $result_int_prop = mysql_query($sql_int_prop, $conn);
           if (mysql_num_rows($result_int_prop) == 0){
              $sql = "select date_format(o.fservicio, '%d/%m/%Y') as fservicio, date_format(o.hcitacion, '%H:%i') as hsalida, date_format(o.hfinservicio, '%H:%i') as hfin,
                      upper(o.nombre) as nombre,  upper(razon_social) as cli, interno, o.km
                      from (select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, vacio
                            from ordenes ord
                            where (ord.fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida)
                      ) o
                      left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
                      left join unidades u on (u.id = o.id_micro) and (u.id_propietario = $_POST[emp])
                      inner join empleados e on e.id_empleado = o.id_chofer_1
                      where not vacio
                      order by interno, fservicio, razon_social, hcitacion";
           }
           else{
              $dat_int_prop = mysql_fetch_array($result_int_prop);
              $sql = "select date_format(o.fservicio, '%d/%m/%Y') as fservicio, date_format(o.hcitacion, '%H:%i') as hsalida, date_format(o.hfinservicio, '%H:%i') as hfin,
                      upper(o.nombre) as nombre,  upper(razon_social) as cli, interno, o.km
                      from (select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, vacio
                            from ordenes ord
                            where (ord.fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida) and (time_to_sec(timediff(concat(fservicio,' ', hcitacion) , '$dat_int_prop[])) >= 0)
                      ) o
                      left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
                      left join unidades u on (u.id = o.id_micro)
                      inner join empleados e on e.id_empleado = o.id_chofer_1
                      where not vacio
                      order by interno, fservicio, razon_social, hcitacion";
           }
     }
     
    /* $sql = "select date_format(o.fservicio, '%d/%m/%Y') as fservicio, date_format(o.hcitacion, '%H:%i') as hsalida, date_format(o.hfinservicio, '%H:%i') as hfin,
             upper(o.nombre) as nombre,  upper(razon_social) as cli, interno, o.km
             from (select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, vacio
                   from ordenes ord
                   where (ord.fservicio between '$desde' and '$hasta') and (id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida)
             ) o
             left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
             left join unidades u on (u.id = o.id_micro)
             inner join empleados e on e.id_empleado = o.id_chofer_1 and e.id_empleador = $_POST[emp] and e.id_estructura = $_SESSION[structure]
             where not vacio
             order by interno, fservicio, razon_social, hcitacion";

     $result = mysql_query($sql, $conn); */
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
                        <th>Fecha de servicio</th>
                        <th>Hora inicio</th>
                        <th>Hora finalizacion</th>
                        <th>Recorrido</th>
                        <th>Interno</th>
                        <th>Cliente</th>
                        <th>Km</th>
                    </tr>';
           while (($data) &&($int == $data['interno'])){
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='center'>$data[0]</td>
                            <td align='center'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='center'>$data[5]</td>
                            <td align='left'>$data[4]</td>
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

