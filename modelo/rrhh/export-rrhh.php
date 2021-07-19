<?
session_start();

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=lista-personal.xls");

include ('../../controlador/bdadmin.php');

$emp="";
          if ($_GET['empl']){
             $emp=" and (e.id_empleador = $_GET[empl])";
          }
          $sql="SELECT upper(c.descripcion) as cargo, e.id_empleado, legajo, upper(concat(apellido, ', ',e.nombre)) as apenom,
                       nrodoc, cuil, upper(domicilio) as domicilio, e.telefono, upper(razon_social) as empleador,
                       date_format(fechanac, '%d/%m/%Y') as fechanac,
                       date_format(inicio_relacion_laboral, '%d/%m/%Y') as fechainicio,
                       if(e.activo, 'checked', '') as activo, es.nombre as str
                FROM empleados e
                left join empleadores em on (em.id = e.id_empleador)
                left join cargo c on (c.id = e.id_cargo)
                left join estructuras es on es.id = e.id_estructura
                where (e.id_estructura in (SELECT id_estructura FROM usuariosxestructuras where id_usuario = $_SESSION[userid])) and (e.activo) and (not borrado) $emp
                order by empleador, $_GET[order]";

          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla = "<table id='example' align='center' border='0' width='100%'>
                     <thead>
            	            <tr>
                                <th>Legjo</th>
                                <th>Apellido, Nombre</th>
                                <th>DNI</th>
                                <th>CUIL</th>
                                <th>Domicilio</th>
                                <th>Telefono</th>
                                <th>Fecha Nac.</th>
                                <th>Fecha Inic. Rel. Laboral</th>
                                <th>Empleador</th>
                                <th>Afectado a...</th>
                                <th>Puesto</th>
                            </tr>
                     </thead>
                     <tbody>";
          while($data = mysql_fetch_array($result)){
                      $tabla.="<tr id='tr-$data[id_empleado]'>
                                   <td class='redi' text='$data[id_empleado]'>$data[legajo]</td>
                                   <td>".htmlentities($data['apenom'])."</td>
                                   <td>$data[nrodoc]</td>
                                   <td>$data[cuil]</td>
                                   <td>$data[domicilio]</td>
                                   <td>$data[telefono]</td>
                                   <td>$data[fechanac]</td>
                                   <td>$data[fechainicio]</td>
                                   <td>".htmlentities($data['empleador'])."</td>
                                   <td>".htmlentities($data['str'])."</td>
                                   <td>".htmlentities($data['cargo'])."</td>";
                      $tabla.= "</tr>";
          }
          $tabla.='</tbody>
                  </table>';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
?>

