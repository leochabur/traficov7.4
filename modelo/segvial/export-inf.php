<?php
session_start();

header("Content-Type: application/vnd.ms-excel");

header("Expires: 0");

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

header("content-disposition: attachment;filename=infracciones.xls");

include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');

     $desde = dateToMysql($_GET['li'], '/');
     $hasta = dateToMysql($_GET['ls'], '/');
     $interno = '';
     if ($_GET['ud']){
           $interno = "and (u.id = $_GET[ud])";
     }
     $cond = '';
     if ($_GET['cnds']){
           $cond = "and $_GET[cnds] in (id_conductor_1, id_conductor_2, id_conductor_3)";
     }
     
     $filtrarf="";
     if (($_GET['li'] != '') && ($_GET['ls'] != '')){
        $filtrarf = "and fecha between '$desde' and '$hasta'";
     }
     
     $sql = "SELECT i.id, interno, if ((id_conductor_2 is null) and (id_conductor_3 is null),
                            concat(e1.apellido, ', ',e1.nombre),
                            concat(if(id_conductor_1 is null, '', e1.apellido), ' // ',if(id_conductor_2 is null, '', e2.apellido),' // ',if(id_conductor_3 is null, '', e3.apellido))) as cond,
                            date_format(fecha, '%d/%m/%Y') as fecha,
       lugar_infraccion, upper(infraccion), importe, upper(c.ciudad), patente, nueva_patente
       FROM infracciones i
       left join empleados e1 on e1.id_empleado = id_conductor_1
       left join empleados e2 on e2.id_empleado = id_conductor_2
       left join empleados e3 on e3.id_empleado = id_conductor_3
       left join tipo_infraccion ti on ti.id = i.id_tipo_infraccion
       left join resolucion_infraccion r on r.id = i.id_resolucion
       left join unidades u on u.id = i.id_coche
       left join ciudades c on c.id = i.id_ciudad
       WHERE not eliminada $filtrarf $cond $interno";

     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);

     
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center" border="1">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Numero</th>
                        <th>Dominio</th>
                        <th>Interno</th>
                        <th>Conductores</th>
                        <th>Fecha</th>
                        <th>Ciudad</th>
                        <th>Ubicacion</th>
                        <th>Infraccion</th>
                        <th>Importe</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     $marcadores = "";
     $i=0;
     while ($data){
               $class = "";
               $tabla.="<tr id='$data[0]' $class>
                            <td align='right'>$data[0]</td>
                            <td>
                            ".
                              ($data['patente']?$data['patente']:$data['nueva_patente'])
                            ."
                            </td>
                            <td align='center'>$data[1]</td>
                            <td align='left'>$data[2]</td>
                            <td align='center'>$data[3]</td>
                            <td align='left'>$data[7]</td>
                            <td align='left'>$data[4]</td>
                            <td align='left'>$data[5]</td>
                            <td align='right'>$data[6]</td>";
               $data = mysql_fetch_array($result);
     }
     $tabla.='</tbody>
              </table>';
    print $tabla;
?>

