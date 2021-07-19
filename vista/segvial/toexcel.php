<?php
     session_start();
     if (!$_SESSION['auth']){
        print "<br><b>La sesion a expirado</b>";
        exit;
     }
     include ('../../controlador/bdadmin.php');
     include ('../../controlador/ejecutar_sql.php');
     include ('../../modelo/utils/dateutils.php');
     
     $desde = dateToMysql($_POST['des'], '/');
     $hasta = dateToMysql($_POST['has'], '/');
     $interno = '';
     if ($_POST['inter']){
           $interno = "and (id = $_POST[inter])";
     }
     $cond = '';
     if ($_POST['conductores']){
           $cond = "and (e.id_empleado = $_POST[conductores])";
     }

     $filtrarf="";
     if (($_POST['desde'] != '') && ($_POST['hasta'] != '')){
        $filtrarf = "and fecha between '$desde' and '$hasta'";
     }

     $sql = "SELECT h.id as numero, date_format(fecha, '%d/%m/%Y') as fecha, date_format(hora, '%H:%i') as hora, legajo, upper(concat(apellido,', ',nombre)) as emple,
       interno, upper(ciudad) as ciudad, upper(direccion_incidente) as dire, descripcion_hecho, organismo
             FROM hechos_vandalicos h
             inner join empleados e on e.id_empleado = h.id_empleado
             inner join ciudades c on c.id = h.lugar_incidente
             left join unidades u on u.id = h.id_micro
             left join organismos_intervinientes_h_v oi on oi.id = h.id_organismo_interviniente
             where not eliminado $filtrarf $cond";
   //  die($sql);
     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $pendi="";


     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Numero</th>
                        <th>Fecha</th>
                        <th>Hora</th>
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Interno</th>
                        <th>Ciudad</th>
                        <th>Ubicacion</th>
                        <th>Descripcion</th>
                        <th>Organismo Interviniente</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     $marcadores = "";
     $i=0;
     while ($data){
               $class = "";
               if ($data['reparada'])
                  $class = "style='background-color: #DCE697;'";
               $tabla.="<tr id='$data[0]' $class>
                            <td align='right'>$data[0]</td>
                            <td align='center'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='right'>$data[3]</td>
                            <td align='left'>$data[4]</td>
                            <td align='right'>$data[5]</td>
                            <td align='left'>$data[6]</td>
                            <td align='left'>$data[7]</td>
                            <td align='left'>$data[8]</td>
                            <td align='left'>$data[9]</td>
                            </tr>";
               $data = mysql_fetch_array($result);
     }
     $tabla.='</tbody>
              </table>';
  header("Content-Disposition: attachment; filename='lista.xls'");
  header("Content-Type: application/vnd.ms-excel");
    print $tabla;
?>

