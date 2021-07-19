<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../vista/paneles/viewpanel.php');
  include_once('../../modelo/utils/dateutils.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion = $_POST['accion'];

  if($accion == 'load'){
          $conn = conexcion();
          $sql = "SELECT legajo, upper(concat(apellido,', ',nombre)) as apenom, date_format(inicio_relacion_laboral, '%d/%m/%Y'),
(SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) FROM vacacionespersonal v where v.id_empleado = e.id_empleado and detalle = 'Saldo Inicial') as deuda,
(SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) FROM vacacionespersonal v where v.id_empleado = e.id_empleado and anio=2014) as actual
                     FROM empleados e
                     WHERE (activo) and (not borrado) and (id_empleador = 1) and (id_cargo = 1)
                     order by apellido";/*

               SELECT legajo, upper(concat(apellido,', ',nombre)) as apenom, date_format(inicio_relacion_laboral, '%d/%m/%Y'),
                                          (

                     (SELECT if(sum(cant_dias) is null, 0, sum(cant_dias)) FROM vacacionespersonal v where v.id_empleado = e.id_empleado)

                     -
                     (SELECT if (sum((datediff(hasta, desde)+1)) is null, 0, sum((datediff(hasta, desde)+1))) as dias
                     FROM novedades n where (n.id_empleado = e.id_empleado) and (id_novedad = 19)and (activa) and (desde > '2014-06-30'))
                     )as tot_vac
                     FROM empleados e
                     WHERE (activo) and (not borrado) and (id_empleador = 1) and (id_cargo = 1)
                     order by apellido";        */

           //die($sql);
          $result = mysql_query($sql, $conn);
          $tabla ='<table id="tablitasssss" align="center" width="70%" class="ui-widget ui-widget-content">
                     <thead>
                            <tr class="ui-widget-header">
                                <th>Legajo</th>
                                <th>Apellido, Nombre</th>
                                <th>Adeudadas</th>
                                <th>2014</th>
                            </tr>
                     </thead>
                     <tbody>';
          while ($data = mysql_fetch_array($result)){
                $color = (($i++%2)==0) ? "#D3D3D3" : "#F3F3F3";
                $tabla.="<tr bgcolor='$color'>
                             <td>$data[0]</td>
                             <td align='left'>$data[1]</td>
                             <td align='right'>$data[3]</td>
                             <td align='right'>$data[4]</td>
                         </tr>";
          }
          $tabla.="</tbody>
                   </table>
                  <style>
                         #tablitasssss { font-size: 85%; }
                         #tablitasssss tbody tr:hover {
                                                 background-color: #FF8080;
                                                 }
                  </style>                   ";
          print $tabla;
  }
?>

