<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldint'){
     $conn = conexcion();

     $sql = "SELECT id, upper(razon_social) as cliente
             FROM clientes c
             where id_estructura = $_POST[str] and activo
             order by cliente";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[0]'>$data[1]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#clientes').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cliente = '';
     if ($_POST['cliente']){
           $cliente = "and (ord.id_cliente = $_POST[cliente])";
     }
     $sql = "select upper(razon_social) as cli, date_format(o.fservicio, '%d/%m/%Y') as fservicio, interno, upper(ori.ciudad), upper(des.ciudad), o.km, comentario , upper(concat(e1.apellido,', ', e1.nombre)) as emple1, upper(concat(e2.apellido,', ', e2.nombre)) as emple2, o.fservicio as fsrv
             from (SELECT fservicio, cr.id_cliente, cr.id_estructura_cliente, id_micro, ord.km, ord.nombre, ord.hcitacion, ord.hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, id_chofer_2
                   FROM ordenes ord
                   inner join servicios s on (s.id = ord.id_servicio) and (s.id_estructura = ord.id_estructura_servicio)
                   inner join cronogramas cr on (cr.id = s.id_cronograma) and (cr.id_estructura = s.id_estructura_cronograma)
                   where (ord.fservicio between '$desde' and '$hasta') and (ord.id_estructura = $_POST[str]) and (not borrada) and (not suspendida) and (cr.claseservicio_id = 4) $cliente
                   union all
                   select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, comentario, id_chofer_1, id_chofer_2
                   from ordenes ord
                   where (fservicio between '$desde' and '$hasta') and (id_estructura = $_POST[str]) and (not borrada) and (not suspendida) and (id_claseservicio = 4) $cliente
             ) o
             left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
             left join unidades u on (u.id = o.id_micro)
             inner join ciudades ori on ori.id = id_ciudad_origen and ori.id_estructura = id_estructura_ciudad_origen
             inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
             left join empleados e1 on e1.id_empleado = o .id_chofer_1
             left join empleados e2 on e2.id_empleado = o .id_chofer_2
             order by razon_social, fsrv, o.hcitacion";

     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $int = $data['cli'];
           $tot = 0;
           $i = 0;
           $tabla.='<tr class="ui-widget-header">
                        <th>Cliente</th>
                        <th>Fecha Servicio</th>
                        <th>Interno</th>
                        <th>Origen</th>
                        <th>Destino</th>
                        <th>km</th>
                        <th>Conductor 1</th>
                        <th>Conductor 2</th>
                        <th>Observaciones</th>
                    </tr>';
           while (($data) &&($int == $data['cli'])){
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='left'>".htmlentities($data[0])."</td>
                            <td align='center'>$data[1]</td>
                            <td align='right'>$data[2]</td>
                            <td align='left'>".htmlentities($data[3])."</td>
                            <td align='left'>".htmlentities($data[4])."</td>
                            <td align='right'>$data[5]</td>
                            <td align='left'>".htmlentities($data[7])."</td>
                            <td align='left'>".htmlentities($data[8])."</td>
                            <td align='left'>$data[6]</td>
                            </tr>";
               $tot=$tot + $data['km'];
               $data = mysql_fetch_array($result);
               $i++;
           }
           $tabla.="<tr><td colspan='9'> <b>Total Km $tot km</b></td></tr>
                    <tr><td colspan='9'><hr align='tr'></td></tr>";
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

