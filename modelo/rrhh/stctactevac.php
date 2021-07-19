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
          $sql = "select descr, id_empleado, round(haber) as haber, debe, round(@imonto:= @imonto + (haber - debe),0) AS Saldos
from( SELECT 'Vacaciones Inical' as descr, id_empleado, cant_dias as haber, 0 as debe, '2014-06-30' as fecha_liquidacion
      FROM vacacionespersonal
      where id_empleado = $_POST[emple] and anio = 0
      union all
      SELECT detalle as descr, id_empleado, cant_dias as haber, 0 as debe, date(fecha_liquidacion)
      FROM vacacionespersonal
      where id_empleado = $_POST[emple] and anio <> 0
      union all
      SELECT concat('Vacaciones (', date_format(desde, '%d/%m/%Y'),' - ',date_format(hasta, '%d/%m/%Y'), ')') as decr, id_empleado, 0.0 as haber, DATEDIFF(hasta, desde) + 1 as debe, desde
      FROM novedades n
      where id_novedad = 19 and
            id_empleado = $_POST[emple] and
            (desde > '2014-06-30') and
            activa
      order by fecha_liquidacion) v, (SELECT @imonto:=0) AS tmp2";

          // die($sql);
          $result = mysql_query($sql, $conn);
          $tabla ='<table id="tablitasssss" align="center" width="70%" class="ui-widget ui-widget-content">
                     <thead>
                            <tr class="ui-widget-header">
                                <th>Detalle</th>
                                <th>Dias liquidados</th>
                                <th>Dias tomados</th>
                                <th>Saldo</th>
                            </tr>
                     </thead>
                     <tbody>';
          while ($data = mysql_fetch_array($result)){
                $color = (($i++%2)==0) ? "#D3D3D3" : "#F3F3F3";
                $tabla.="<tr bgcolor='$color'>
                             <td>$data[0]</td>
                             <td align='right'>$data[2]</td>
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

