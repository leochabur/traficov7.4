<?
session_start();

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=pedidos_explicacion.xls");

include ('../../controlador/bdadmin.php');
     $desde = $_GET['desde'];
     $hasta = $_GET['hasta'];
     $cond = '';
     if ($_GET['cond']){
           $cond = "and (d.id_empleado = $_GET[cond])";
     }
     $res = ($_GET['res'])?"and resolucion is null":"";
     $ent = ($_GET['ent'])?"and fecha_entrega is null":"";

     $sql = "SELECT concat(des.apellido, ', ', des.nombre) as dest, date_format(fecha_emision, '%d/%m/%Y') as emi, date_format(fecha_entrega, '%d/%m/%Y') as entre,
       concat(sol.apellido, ', ', sol.nombre) as sol, mediante, descripcion_hecho, rs.descripcion, d.id, nro_descargo
FROM descargos d
left join empleados des on des.id_empleado = d.id_empleado
left join empleados sol on sol.id_empleado = d.id_solicitante
left join resolucionSiniestro rs on rs.id = d.resolucion
where not eliminado and fecha_emision between '$desde' and '$hasta' $cond $res $ent
order by nro_descargo";


          $conn = conexcion();
          $result = mysql_query($sql, $conn);
          $tabla='<table id="example" name="example" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Nro Descargo</th>
                        <th>Destinatario</th>
                        <th>F. Emision</th>
                        <th>F, Entrega</th>
                        <th>Solicitante</th>
                        <th>Mediante...</th>
                        <th>Descripcion Hecho</th>
                        <th>Resolucion</th>
                    </tr>
                    </thead>
                    <tbody>';
    //      die($tabla);

     while ($data = mysql_fetch_array($result)){
               $class = "";
               if ($data['reparada'])
                  $class = "style='background-color: #DCE697;'";
               $tabla.="<tr id='$data[7]' $class>
                            <td align='left'>$data[nro_descargo]</td>
                            <td align='left'>$data[0]</td>
                            <td align='center'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='left'>$data[4]</td>
                            <td align='left'>$data[5]</td>
                            <td align='left'>$data[6]</td>
                            </tr>";
     }
          $tabla.='</tbody>
                  </table>';
          mysql_free_result($result);
          cerrarconexcion($conn);
          print $tabla;
?>

