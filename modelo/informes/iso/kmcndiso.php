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
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

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
     $tabla='<a href="/modelo/informes/iso/exportkmcnd.php?des='.$desde.'&has='.$hasta.'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>
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
  }
  
?>

