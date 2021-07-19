<?php
  set_time_limit(0);
  session_start();
  ////////////////// modulo para dar de alta y mdificar una unidad en la BD  /////////////////////
  include ('../../../controlador/bdadmin.php');
  include ('../../../controlador/ejecutar_sql.php');


  $accion = $_POST['accion'];

  if($accion == 'load')
  {
    
      $desde = DateTime::createFromFormat('d/m/Y', $_POST['desde']);      
      $hasta = DateTime::createFromFormat('d/m/Y', $_POST['hasta']);  

          $sql = "SELECT *, date_format(fechaHoraEvento,'%d/%m/%Y - %H:%i:%s') as evento, 
                            date_format(fechaHoraRecepcion,'%d/%m/%Y - %H:%i:%s') as recepcion,
                            date_format(fechaOrden,'%d/%m/%Y') as forden,
                            time_format(horaLlegadaDiagrama,'%H:%i') as fllega
                  FROM comunicacionesUrbeTrack
                  where date(fechaHoraEvento) between '".$desde->format('Y-m-d')."' AND '".$hasta->format('Y-m-d')."'
                  ORDER BY fechaHoraEvento";

          $result = ejecutarSQL($sql);

          $tabla ='<table id="tablitasssss" align="center" width="100%" class="table table-zebra">
                     <thead>
                            <tr>
                                <th align="center">Fecha Evento</th>
                                <th align="center">Fecha Recepcion</th>
                                <th align="center">Interno</th>
                                <th align="center">Cliente</th>
                                <th align="center">Servicio</th>
                                <th align="center">Fecha Orden</th>
                                <th align="center">Hora Llegada Diagrama</th>
                                <th align="center">Numero Orden</th>
                                <th align="center">Accion</th>
                                <th align="center">Cant. Registros</th>
                            </tr>
                     </thead>
                     <tbody>';

          
          while ($res = mysql_fetch_array($result))
          {
            $tabla.="<tr>
                        <td>$res[evento]</td>
                        <td>$res[recepcion]</td>
                        <td>$res[interno]</td>
                        <td>$res[cliente]</td>
                        <td>$res[servicio]</td>
                        <td>$res[forden]</td>
                        <td>$res[fllega]</td>
                        <td>$res[numeroOrden]</td>
                        <td>$res[accion]</td>
                        <td>$res[numeroRegistros]</td>
                      </tr>";
          }

          $tabla.="</tbody>
                   </table>";
          print $tabla;
  }
?>

