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
     $prop = ($_POST['fle']?"where id = $_POST[fle]":"");

     $sql = "select interno, upper(e.razon_social) as propietario, upper(c.razon_social) as cliente, count(*), round(sum(peajes), 2)
             from ordenes o
             inner JOIN unidades m ON (m.id = o.id_micro)
             inner join (select * from empleadores $prop) e on e.id = m.id_propietario and e.id_estructura = m.id_estructura_propietario
             inner join tipounidad tu on tu.id = m.id_tipounidad  and tu.id_estructura = m.id_estructura_tipounidad
             inner JOIN clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
             where (peajes > 0) and (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida)
             group by m.id, c.id
             order by e.razon_social, interno, c.razon_social";
             

     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $tabla='<a href="/modelo/informes/cria/exportpjecliente.php?des='.$desde.'&has='.$hasta.'&str='.$_POST['str'].'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>';
     $tabla.='<table id="example" name="example" class="ui-widget ui-widget-content" width="75%" align="center">
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $cli = $data[1];
           $tabla.='<tr>
                        <th colspan="4" class="ui-widget-header">'.htmlentities($data[1]).'</th>
                    </tr>
                    <tr class="ui-widget-header">
                        <th>Interno</th>
                        <th>Cliente</th>
                        <th>Cant. Pasadas</th>
                        <th>Importe Total</th>
                    </tr>';
           $tot = 0;
           $i=0;
           while (($data) &&($cli == $data[1])){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                             <td align='right'>$data[0]</td>
                             <td align='left'>$data[2]</td>
                             <td align='right'>$data[3]</td>
                             <td align='right'>$data[4]</td>
                        </tr>";
                 $tot+=$data[4];
                 $data = mysql_fetch_array($result);
                 $i++;
           }
           $tabla.="<tr><td colspan='4' align='right'><b>TOTAL DE PEAJES: \$ ". number_format($tot,2)."</b> </td></tr>
                    <tr><td colspan='4' align='right'></td></tr> ";
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

