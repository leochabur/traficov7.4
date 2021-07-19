<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $str = $_POST['str'];
     
     $conn = conexcion();

     $sql = "SELECT id_micro, sum(km)
             FROM ordenes o
             where (fservicio between '$desde' and '$hasta') and (id_estructura = $str) and (not suspendida) and (not borrada) and (id_micro is not null)
             group by id_micro";
     $result_km = mysql_query($sql);
     $tot_km=array();
     while ($data = mysql_fetch_array($result_km)){
        $tot_km[$data[0]] = $data[1];
     }
     $sql = "SELECT count(*), sum(km), interno, u.id, upper(razon_social)
             FROM (select id_cliente, id_estructura_cliente, id_micro, id_estructura, km
                   from ordenes o
                   where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (id_cliente_vacio is null)
                   union all
                   select id_cliente_vacio, id_estructura_cliente_vacio, id_micro, id_estructura, km
                   from ordenes o
                   where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (o.id_estructura = $str) and (id_cliente_vacio is not null)
             ) o
             inner join estructuras e on e.id = o.id_estructura
             inner join unidades u on u.id = o.id_micro
             inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura
             group by o.id_micro, o.id_cliente
             order by interno";

     $result = mysql_query($sql, $conn);
     $data = mysql_fetch_array($result);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>';
     while ($data){
           $int = $data[3];
           $tabla.='<tr class="ui-widget-header">
                       <th colspan="4">INTERNO '.$data[2].'</th>
                   </tr>
                   <tr class="ui-widget-header">
                        <th>Cliente</th>
                        <th>Km recorridos</th>
                        <th>Cant. Servicios</th>
                        <th>%</th>
                   </tr>';
           while (($data)&&($int == $data[3])){
               $tabla.="<tr'>
                            <td align='left'>".htmlentities($data[4])."</td>
                            <td align='right'>$data[1]</td>
                            <td align='right'>$data[0]</td>
                            <td align='right'>".round((($data[1]/$tot_km[$int])*100),4)." %</td>
                            </tr>";
               $data = mysql_fetch_array($result);
           }
            $tabla.="<tr><td colspan='9'><hr align='tr'></td></tr>";
     }


     $tabla.='</tbody>
              </table>
                  <style>

                         #example tbody tr:hover {
                                        background-color: #FF8080; }
                         #example tbody tr {cursor: pointer}

}
                  </style>
                  <script type="text/javascript">
                          $("#example tr:odd").css("background-color", "#ddd");
                          $("#example tr:even").css("background-color", "#ccc");

                  </script>';
    print $tabla;
  }
?>

