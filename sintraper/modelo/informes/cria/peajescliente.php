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

     $sql = "select upper(razon_social) as razon_social, upper(tu.tipo) as tipo, count(*), round(sum(peajes), 2)
from ordenes o
inner JOIN unidades m ON (m.id = o.id_micro)
inner join tipounidad tu on tu.id = m.id_tipounidad
inner JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
where (peajes > 0) and (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_POST[str]) and (not borrada) and (not suspendida) and (id_cliente_vacio is null)
group by c.id, tu.id
union all
select upper(razon_social) as razon_social, 'VACIO' as tipo, count(*), round(sum(peajes), 2)
from ordenes o
inner JOIN unidades m ON (m.id = o.id_micro)
inner join tipounidad tu on tu.id = m.id_tipounidad
inner JOIN clientes c ON ((c.id = o.id_cliente_vacio) and (c.id_estructura = o.id_estructura_cliente_vacio))
where (peajes > 0) and (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_POST[str]) and (not borrada) and (not suspendida)
group by c.id
order by razon_social, tipo";
             

     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $tabla='<a href="/modelo/informes/cria/exportpjecliente.php?des='.$desde.'&has='.$hasta.'&str='.$_POST['str'].'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>';
     $tabla.='<table id="example" name="example" class="ui-widget ui-widget-content" width="50%" align="center">
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $cli = $data[0];
           $tabla.='<tr>
                        <th colspan="3" class="ui-widget-header">'.htmlentities($data[0]).'</th>
                    </tr>
                    <tr class="ui-widget-header">
                        <th>Tipo Vehiculo</th>
                        <th>Cant. Serv</th>
                        <th>Importe</th>
                    </tr>';
           $tot = 0;
           $i=0;
           while (($data) &&($cli == $data[0])){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                             <td>$data[1]</td>
                             <td align='right'>$data[2]</td>
                             <td align='right'>$data[3]</td>
                        </tr>";
                 $tot+=$data[3];
                 $data = mysql_fetch_array($result);
                 $i++;
           }
           $tabla.="<tr><td colspan='3' align='right'><b>TOTAL DE PEAJES: \$ $tot</b> </td></tr>";
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

