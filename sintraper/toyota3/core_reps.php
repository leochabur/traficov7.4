<?php
     include_once( 'bdadmin.php' );
     include_once( 'dateutils.php' );


     function cantSevXTurno($turno, $desde, $hasta, $cliente, $conn){
              $sql = "select count(*)
                      from ordenes o
                      inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                      where o.id_estructura = 1 and fservicio between '$desde' and '$hasta' and o.id_cliente = $cliente and s.id_turno = $turno";
              $result = mysql_query($sql, $conn);
              if ($r = mysql_fetch_array($result))
                 return $r[0];
              else
                  return 0;
     }

     function loadReps($desde, $hasta, $cliente){
        $desde = dateToMysql($desde, '/');
        $hasta = dateToMysql($hasta, '/');
      //  $cliente = 10;
        $conn = conexcion();

        $sql = "select tu.id, upper(turno), count(*)
                         from ordenes o
                         inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                         inner join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                         where o.id_estructura = 1 and fservicio between '$desde' and '$hasta' and o.id_cliente = $cliente
                         group by id_turno";

        $result = mysql_query($sql, $conn);
        $api='<script type="text/javascript">
                       google.load("visualization", "1.1", {packages:["table", "corechart"]});
                       google.setOnLoadCallback(drawTable);

                       function drawTable() {
                                            var data = new google.visualization.DataTable();
                                            data.addColumn(\'string\', \'Turno / Servicio\');
                                            data.addColumn(\'number\', \'Cantidad Servicios\');
                                            data.addRows([';
         $i=0;
         while ($data = mysql_fetch_array($result)){
               if ($i==0)
                  $api.="['$data[1]', $data[2]]";
               else
                   $api.=",['$data[1]', $data[2]]";
               $i++;
         }
        $api.="]);

        var table = new google.visualization.Table(document.getElementById('table_div'));

        table.draw(data);
        var chart = new google.visualization.PieChart(document.getElementById('chart_sort_div'));
        chart.draw(data);
      }
    </script>";
     
     
        /*$desde = dateToMysql($desde, '/');
        $hasta = dateToMysql($hasta, '/');
      //  $cliente = 10;
        $conn = conexcion();

        $sql = "select tu.id, upper(turno), upper(c.nombre), count(*)
                         from ordenes o
                         inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                         inner join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                         INNER JOIN cronogramas c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                         where o.id_estructura = 1 and fservicio between '$desde' and '$hasta' and o.id_cliente = $cliente
                         group by id_turno, s.id
                         order by id_turno, c.nombre";

        $result = mysql_query($sql, $conn);
        $count = mysql_num_rows($result);
        $aux = 1;
        $tablaEntrada= "<table id='examplebasic'>
                           <thead>

                                <tr>
                                    <th>Turno / Servicio</th>
                                    <th>Cantidad Servicios</th>
                                </tr>
                           </thead>
                           <tbody>";

        $data = mysql_fetch_array($result);
        while ($data){
              $turno = $data[0];
              $tablaEntrada.="<tr data-tt-id='$data[0]'>
                                  <td>$data[1]</td>
                                  <td>".cantSevXTurno($turno, $desde, $hasta, 10, $conn)."</td>
                              </tr>";
              while ($turno == $data[0]){
                    $tablaEntrada.="<tr data-tt-parent-id='$data[0]'>
                                        <td>$data[2]</td>
                                        <td>$data[3]</td>
                                    </tr>";
                    $data = mysql_fetch_array($result);
              }

        }
        $tablaEntrada.="</tbody>
                         </table>
                         ";
        mysql_close($conn);
        return $tablaEntrada;   */
        return $api;
        }
?>
