<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT upper(razon_social) as nombre,  id
             FROM clientes c
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>".htmlentities($data[0])."</option>";
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
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

   /*  $sql = "Select nombre, count(*) as cant, 'Sin coche asignado' as tipo, null as precio, null as total
              from (select * from ordenes where fservicio between '$desde' and '$hasta' and id_cliente = $_POST[cli] and id_estructura = $_POST[str] and id_micro is null) o
              inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
              group by s.id_cronograma
              union all
              select nombre, cant, tipo, precio, (precio * cant) as total
              from (
              select count(*) as cant, nombre, s.id_cronograma, tu.id as id_tipo, s.id_estructura_cronograma as estrcron, id_cliente, id_estructura_cliente, tu.tipo
              from (select * from ordenes where fservicio between '$desde' and '$hasta' and id_cliente = $_POST[cli] and id_estructura = $_POST[str] and id_micro is not null) o
              inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
              left join unidades u on u.id = o.id_micro
              left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
              group by s.id_cronograma, tu.id) o
              left join precioTramoServicio pts on pts.id_cronograma = o.id_cronograma and pts.id_estructuraCronograma = estrcron and
                                                   id_tipoUnidad = o.id_tipo and pts.id_cliente = o.id_cliente and id_estructuraCliente = id_estructura_cliente";     */

    $sql="select fecha, interno, nombre, time_format(hsalida, '%H:%i'), time_format(hfin, '%H:%i'), time_format(timediff(concat(if(hsalida > hfin, adddate(fservicio, interval 1 day), fservicio), ' ', hfin), concat(fservicio,' ', hsalida)), '%H:%i') as cantHs
from(
SELECT date_format(fservicio, '%d/%m/%Y') as fecha, interno, hsalida, if(hfinservicioreal is null,hfinservicio, hfinservicioreal) as hfin, fservicio, upper(nombre) as nombre
FROM ordenes o
left join unidades u on u.id = o.id_micro
where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and not borrada and not suspendida and o.id_cliente = $_POST[cli] ) o
order by nombre, fservicio, interno";
    // die($sql);




     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="75%" id="example" name="example" class="ui-widget ui-widget-content" align="center">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Fecha</th>
                        <th id="razon_social">Interno</th>
                        <th id="interno">Servicio</th>
                        <th id="fservicio">H. Inicio</th>
                        <th id="nombre">H. Fin</th>
                        <th id="nombre">Cant. Horas</th>
                    </tr>';
     $totaf=0;
     while ($data){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='center'>".htmlentities($data[0])."</td>
                              <td align='right'>$data[1]</td>
                              <td align='left'>$data[2]</td>
                              <td align='right'>$data[3]</td>
                              <td align='right'>$data[4]</td>
                              <td align='right'>$data[5]</td>
                          </tr>";
                 $data = mysql_fetch_array($result);
                 $i++;
     }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">

                  </script>
';
    print $tabla;
  }
  
?>

