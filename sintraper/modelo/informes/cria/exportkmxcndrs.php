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
  
     $desde = $_GET['desde'];
     $hasta = $_GET['hasta'];
     $km = $_GET['km'];

     $conn = conexcion();

     $sql = "SELECT upper(concat(apellido,', ',em.nombre)), upper(razon_social), upper(e.nombre), count(*), sum(km)
             from(
                  select id_chofer_1 as emple, id_estructura, km
                  FROM ordenes o
                  where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (id_chofer_1 is not null)
                  union all
                  select id_chofer_2 as emple, id_estructura, km
                  FROM ordenes o
                  where (fservicio between '$desde' and '$hasta') and (not suspendida) and (not borrada) and (id_chofer_2 is not null)
                  )o
             inner join estructuras e on e.id = o.id_estructura
             left join empleados em on em.id_empleado = o.emple
             LEFT JOIN empleadores emp ON (emp.id = em.id_empleador) and (emp.id_estructura = em.id_estructura_empleador)
             group by o.emple
             order by e.id, emp.id, apellido, em.nombre";
     $result = mysql_query($sql, $conn);
     
  $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>
                    <tr class="ui-widget-header">
                        <th>Apellido, Nombre</th>
                        <th>Empleador</th>
                        <th>Estructura</th>
                        <th>Total Km</th>
                        <th>Total Servicios</th>
                        <th>%</th>
                    </tr>';
     while ($data = mysql_fetch_array($result)){
               $tabla.="<tr bgcolor='$color' id='$data[3]'>
                            <td align='left'>".htmlentities($data[0])."</td>
                            <td align='left'>".htmlentities($data[1])."</td>
                            <td align='left'>".htmlentities($data[2])."</td>
                            <td align='right'>$data[4]</td>
                            <td align='right'>$data[3]</td>
                            <td align='right'>".str_replace('.',',',round((($data[4]/$km)*100),4))."</td>
                            </tr>";
     }
           $tabla.="<tr><td colspan='9'><hr align='tr'></td></tr>";

     $tabla.='</tbody>
              </table>';

    print $tabla;
  
?>

