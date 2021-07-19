<?

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=servclient.xls");

include ('../../../controlador/bdadmin.php');

     $desde = $_GET['des'];
     $hasta = $_GET['has'];

     $sql = "SELECT legajo, upper(concat(apellido,', ',nombre)) as nom_chofer, sum(km) as km, count(*) as cantserv
             from(
                  select km, id_chofer_1 as chofer, fservicio
                  from ordenes
                  where id_chofer_1 is not null
                  union all
                  select km, id_chofer_2 as chofer, fservicio
                  from ordenes
                  where id_chofer_2 is not null) o
             inner join empleados e on e.id_empleado = o.chofer
             where  fservicio between '$desde' and '$hasta'
             group by chofer
             order by nom_chofer";

     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<a href="/modelo/informes/trafico/exportkmcli.php?des='.$desde.'&has='.$hasta.'&cli='.$cliente.'&str='.$struct.'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
             <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Cant. Servicios</th>
                        <th>Km Recorridos</th>
                    </tr>
             </thead>
             <tbody>';
     $i = 0;
     while ($data = mysql_fetch_array($result)){
           $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
           $tabla.="<tr bgcolor='$color'>
                              <td align='left'>$data[0]</td>
                              <td align='left'>".htmlentities($data[1])."</td>
                              <td align='center'>$data[3]</td>
                              <td align='right'>$data[2]</td>
                    </tr>";
           $i++;
     }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>';
    print $tabla;
?>

