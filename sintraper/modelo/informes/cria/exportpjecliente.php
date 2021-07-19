<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
header("Content-type: application/octet-stream");
//indicamos al navegador que se está devolviendo un archivo
header("Content-Disposition: attachment; filename=kmxconductor.xls");
//con esto evitamos que el navegador lo grabe en su caché
header("Pragma: no-cache");
header("Expires: 0");
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  
     $desde = $_GET['des'];
     $hasta = $_GET['has'];
     $str = $_GET['str'];

     $conn = conexcion();

     $sql = "select upper(razon_social) as razon_social, upper(tu.tipo) as tipo, count(*), round(sum(peajes), 2)
             from ordenes o
             inner JOIN unidades m ON (m.id = o.id_micro)
             inner join tipounidad tu on tu.id = m.id_tipounidad
             inner JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
             where (peajes > 0) and (fservicio between '$desde' and '$hasta') and (o.id_estructura = $str) and (not borrada) and (not suspendida) and (id_cliente_vacio is null)
             group by c.id, tu.id
             union all
             select upper(razon_social) as razon_social, 'VACIO' as tipo, count(*), round(sum(peajes), 2)
             from ordenes o
             inner JOIN unidades m ON (m.id = o.id_micro)
             inner join tipounidad tu on tu.id = m.id_tipounidad
             inner JOIN clientes c ON ((c.id = o.id_cliente_vacio) and (c.id_estructura = o.id_estructura_cliente_vacio))
             where (peajes > 0) and (fservicio between '$desde' and '$hasta') and (o.id_estructura = $str) and (not borrada) and (not suspendida)
             group by c.id
             order by razon_social, tipo";
     $result = mysql_query($sql, $conn);
     
 $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="50%" align="center">
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
              </table>';
    print $tabla;

  
?>

