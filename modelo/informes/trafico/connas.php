<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if (true){
     $conn = conexcion();
     $fecha = $_POST['fecha'];
     $sql = "SELECT legajo, concat(apellido, ', ', nombre) as apenom, nrodoc
             FROM empleados e
             inner join empleadores emp on emp.id = e.id_empleador
             where (e.id_estructura = 1) and (not e.borrado)and (emp.id in (1, 51)) and (emp.activo)and (e.activo) and (id_cargo = 1) and (e.id_empleado not in(
                                                                                           select id_empleado
                                                                                           from novedades
                                                                                           where ('$fecha' between desde and hasta) and (activa)
                                                                                           union all
                                                                                           select id_chofer_1
                                                                                           from (select * from ordenes where fservicio = '$fecha') o
                                                                                           where fservicio = '$fecha' and not borrada and not suspendida and id_estructura = 1 and id_chofer_1 is not null
                                                                                           union all
                                                                                           select id_chofer_2
                                                                                           from (select * from ordenes where fservicio = '$fecha') o
                                                                                           where fservicio = '$fecha' and not borrada and not suspendida and id_estructura = 1 and id_chofer_2 is not null))
             order by apellido";
  //   die($sql);
     $result = mysql_query($sql, $conn);

     $tabla.= "<table width='80%' >
                           <thead class='ui-widget ui-widget-header ui-corner-all'>
                                <tr>
                                    <th>Legajo</th>
                                    <th>Apellido, Nombre</th>
                                    <th>DNI</th>
                                </tr>
                           </thead>
                           <tbody>";
     $i=0;
     while ($data = mysql_fetch_array($result)){
                       $color = (($i++%2)==0)?'#D0D0D0':'#B0B0B0';
                       $tabla.="<tr bgcolor='$color'>
                                    <td width='10%'>$data[0]</td>
                                    <td width='80%'>".htmlentities($data[1])."</td>
                                    <td width='10%'>$data[2]</td>
                                </tr>";
     }
     $tabla.="</tbody></table><br>";
     $tabla.="<script type='text/javascript'>
              </script>";
     print $tabla;
  }
  
?>

