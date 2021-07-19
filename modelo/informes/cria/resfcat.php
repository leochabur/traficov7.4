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

    $sql="select id, cliente, sum(cantidad) as cantidad, sum(importe) as importe, sum(km) as km
from(
select cl.id, upper(razon_social) as cliente, count(*) as cantidad, round((count(*) * precio), 2) as importe, sum(o.km) as km
from ordenes o
inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
inner join tipofacturacioncliente tfc on tfc.id_cliente = o.id_cliente and tfc.id_estructuracliente = o.id_estructura_cliente
inner join clientes cl on cl.id = o.id_cliente and cl.id_estructura = o.id_estructura_cliente
left join unidades u on u.id = o.id_micro
left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
left join precioTramoServicio ptm on ptm.id_cronograma = s.id_cronograma and ptm.id_estructuraCronograma = s.id_estructura_cronograma
          and u.id_tipounidad = ptm.id_tipoUnidad and u.id_estructura_tipounidad = ptm.id_estructuraTipoUnidad
where fservicio between '$desde' and '$hasta'  and o.id_estructura = $_POST[str] and not borrada and not suspendida and facturaPorTramo
group by cl.id, c.id, tu.id
) p
union all
select cl.id, upper(razon_social) as cliente, count(*) as cantidad, montoMensualFacturacion as importe, sum(o.km) as km
from ordenes o
inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
inner join tipofacturacioncliente tfc on tfc.id_cliente = o.id_cliente and tfc.id_estructuracliente = o.id_estructura_cliente
inner join clientes cl on cl.id = o.id_cliente and cl.id_estructura = o.id_estructura_cliente
where fservicio between '$desde' and '$hasta'  and o.id_estructura = $_POST[str] and not borrada and not suspendida and not facturaPorTramo
order by cliente";
  //   die($sql);




     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Cliente</th>
                        <th id="razon_social">Cantidad servicios</th>
                        <th id="interno">Km Recorridos</th>
                        <th id="nombre">$/Km</th>
                        <th id="nombre">Total facturacion</th>
                    </tr>';
     $totaf=0;
     while ($data){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>".htmlentities($data[cliente])."</td>
                              <td align='left'>".$data[cantidad]."</td>
                              <td align='right'>".number_format($data[km],2)."</td>
                              <td align='right'>".number_format($data[importe]/$data[km],2)."</td>
                              <td align='right'>$".number_format($data[importe],2)."</td>
                          </tr>";
                 $totaf+= $data[importe];
                 $data = mysql_fetch_array($result);
                 $i++;
     }
     $tabla.='<tr><td colspan="4"><b>Importe Total</b></td><td align="right"><b>$ '.number_format($totaf,2).'</b></td></tr></tbody>
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

