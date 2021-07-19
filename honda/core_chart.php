<?php
     include_once( 'bdadmin.php' );
     include_once( 'dateutils.php' );
     
     function createFunctionChart($nomFunction, $datos, $nomDiv, $title){
              $function = "function $nomFunction() {
                                   var datavta = google.visualization.arrayToDataTable($datos);
                                   var options = {
                                               title: '$title',
                                               hAxis: {
                                               format: 'd/M'
                                               },
                                               curveType: 'function',
                                               pointSize: 15
                                               };
                                   var chart = new google.visualization.LineChart(document.getElementById('$nomDiv'));
                                   chart.draw(datavta, options);
                          }";
              return $function;
     }

     function titulo($servicio,$origen,$destino){
           if (($servicio) || ($origen) || ($destino)){ //si se filtra por algun otro filtro origen/destino/servicio solo genera un grafico
              $conn = conexcion();
              if ($servicio){
                 $result = mysql_query("SELECT nombre FROM cronogramas WHERE id = $servicio", $conn);
                 if ($data = mysql_fetch_array($result)){
                    $titulo = "SERVICIO: $data[0]";
                 }
              }
              else{
                  if ($origen){
                     $result = mysql_query("SELECT ciudad FROM ciudades WHERE id = $origen", $conn);
                     if ($data = mysql_fetch_array($result)){
                        $titulo = "ORIGEN: $data[0]";
                     }
                  }
                  if ($destino){
                     $result = mysql_query("SELECT ciudad FROM ciudades WHERE id = $destino", $conn);
                     if ($data = mysql_fetch_array($result)){
                        if ($origen){
                           $titulo.= " - DESTINO: $data[0]";
                        }
                        else{
                             $titulo = "DESTINO: $data[0]";
                        }
                     }
                  }
              }
              mysql_close($conn);
           }
           return $titulo;
     }

     function graphConfiabilidad($fdesde, $fhasta, $origen, $destino, $servicio, $t_serv, $turno, $i_v){
        $desde = dateToMysql($fdesde, '/');
        $hasta = dateToMysql($fhasta, '/');
        $conn = conexcion();
        //estructura basica para todas las consultas independiente de los filtros aplicados
        $calculo_conf = "cast(if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0))) as decimal(5,2))";
        if (($i_v == 'v') || ($origen == 3)){
        $calculo_conf = "cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2))";
        }
        if ($servicio){
           $sql_srv = "select ciudades_id_origen from cronogramas c where id = $servicio";
           $res = mysql_query($sql_srv, $conn);
           if ($srv = mysql_fetch_array($res)){
              if ($srv[0] == 3){
                         $calculo_conf = "cast(if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2))";
              }
           }
        }
        
        
        $sql="SELECT date_format(fservicio, '%d'), $calculo_conf, date_format(fservicio, '%d/%m/%Y'), fservicio as auxi, fservicio, day(fservicio), (month(fservicio)-1), year(fservicio)
                 FROM (select * from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o ";
        $group_by="ORDER BY fservicio";

        if ((!$servicio) && (!$origen) && (!$destino)){ //se filtra por tipo de servicio y turno
           $sql.="INNER JOIN (select * from servicios where i_v = '$i_v') s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 INNER JOIN (SELECT * from tiposervicio WHERE id = $t_serv) ts ON ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 INNER JOIN (SELECT * from turnos WHERE id = $turno) tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno ";
        }
        else{
             $sql.="INNER JOIN servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";
        }


        if ($servicio){
           $sql.="INNER JOIN (select * from cronogramas where id = $servicio) c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma ";
        }
        else
           $sql.="INNER JOIN cronogramas c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma ";
           
        if ($origen)
           $sql.="INNER JOIN (select * from ciudades where id = $origen) ori ON ori.id = c.ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen ";
        if ($destino)
           $sql.="INNER JOIN (select * from ciudades where id = $destino) des ON des.id = c.ciudades_id_destino and des.id_estructura = c.ciudades_id_estructura_destino ";

        $sql.=" $group_by";
       // print $sql;
       // die();


        $result = mysql_query($sql, $conn);
        $count = mysql_num_rows($result);
        $aux = 1;
        $tablaEntrada= "<table class='ui-widget ui-corner-all' width='50%'>
                           <thead class='ui-widget ui-widget-header'>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad Servicios</th>
                                    <th>% Confiabilidad</th>
                                </tr>
                           </thead>
                           <tbody>";
        $count = mysql_num_rows($result);
        $aux = 1;
        $j=0;
        $datos="[[{type: 'date', label: 'Fecha'}, 'Confiabilidad'],";
        $data = mysql_fetch_array($result);
        while ($data){
              $fecha_aux = $data[0];
              $fecha_aux_completa = $data[2];
              $suma_aux = 0;
              $cant_aux = 0;
              while ($fecha_aux == $data[0]){
                    $suma_aux+=$data[1];
                    $cant_aux++;
                    $anio = $data[7];
                    $mes = $data[6];
                    $dia =  $data[5];
                    $fecha_diaria = $data[3];
                    $data = mysql_fetch_array($result);
              }
              $promedio=0.0;
              $promedio = round(($suma_aux/$cant_aux)*100,2);
              if (($j%2) == 0)
                    $color = '#D0D0D0';
              else
                     $color = '#FFFFFF';
              $auxid=$t_serv+'v';
              
              $ori = str_pad($origen, 6 , "000000", STR_PAD_LEFT);
              $des = str_pad($destino, 6 , "000000", STR_PAD_LEFT);
              $srv = str_pad($servicio, 6 , "000000", STR_PAD_LEFT);
              $tipo = str_pad($t_serv, 6 , "000000", STR_PAD_LEFT);
              $tno = str_pad($turno, 6 , "000000", STR_PAD_LEFT);
              
              $tablaEntrada.="<tr style='cursor: pointer;' class='show' bgcolor='$color' id='".$fecha_diaria.$ori.$des.$srv.$tipo.$tno.$i_v."c'>
                              <td align='center'>$fecha_aux_completa</td>
                              <td align='right'>$cant_aux</td>
                              <td align='right'>$promedio</td>
                             </tr>";
              $j++;

              $datos.="[new Date($anio, $mes, $dia), $promedio]";
              if ($data)
                 $datos.=',';
        }
        $datos.="]";
       // print $datos;
       // die();
        $tablaEntrada.="</tbody></table>";
        mysql_close($conn);
        return array($datos, $tablaEntrada);
     
     }
     
function graphEficiencia($fdesde, $fhasta, $origen, $destino, $servicio, $t_serv, $turno, $i_v){
        $desde = dateToMysql($fdesde, '/');
        $hasta = dateToMysql($fhasta, '/');
        $conn = conexcion();
        //estructura basica para todas las consultas independiente de los filtros aplicados
        $calculo_conf = "(cantpax/cantasientos)";

        $sql="SELECT date_format(fservicio, '%d'), $calculo_conf, date_format(fservicio, '%d/%m/%Y'), fservicio as auxi, fservicio, day(fservicio), (month(fservicio)-1), year(fservicio)
                 FROM (select * from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o
                 inner join unidades m ON (m.id = o.id_micro) ";
        $group_by="ORDER BY fservicio";

        if ((!$servicio) && (!$origen) && (!$destino)){ //se filtra por tipo de servicio y turno
           $sql.="INNER JOIN (select * from servicios where i_v = '$i_v') s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 INNER JOIN (SELECT * from tiposervicio WHERE id = $t_serv) ts ON ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 INNER JOIN (SELECT * from turnos WHERE id = $turno) tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno ";
        }
        else{
             $sql.="INNER JOIN servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";
        }


        if ($servicio){
           $sql.="INNER JOIN (select * from cronogramas where id = $servicio) c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma ";
        }
        else
           $sql.="INNER JOIN cronogramas c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma ";

        if ($origen)
           $sql.="INNER JOIN (select * from ciudades where id = $origen) ori ON ori.id = c.ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen ";
        if ($destino)
           $sql.="INNER JOIN (select * from ciudades where id = $destino) des ON des.id = c.ciudades_id_destino and des.id_estructura = c.ciudades_id_estructura_destino ";

        $sql.=" $group_by";
       // print $sql;
       // die();


        $result = mysql_query($sql, $conn);
        $count = mysql_num_rows($result);
        $aux = 1;
        $tablaEntrada= "<table class='ui-widget ui-corner-all' width='50%'>
                           <thead class='ui-widget ui-widget-header'>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Cantidad Servicios</th>
                                    <th>% Eficiencia</th>
                                </tr>
                           </thead>
                           <tbody>";
        $count = mysql_num_rows($result);
        $aux = 1;
        $j=0;
        $datos="[[{type: 'date', label: 'Fecha'}, 'Eficiencia'],";
        $data = mysql_fetch_array($result);
        while ($data){
              $fecha_aux = $data[0];
              $fecha_aux_completa = $data[2];
              $suma_aux = 0;
              $cant_aux = 0;
              while ($fecha_aux == $data[0]){
                    $suma_aux+=$data[1];
                    $cant_aux++;
                    $anio = $data[7];
                    $mes = $data[6];
                    $dia =  $data[5];
                    $fecha_diaria = $data[3];
                    $data = mysql_fetch_array($result);
              }
              $promedio=0.0;
              $promedio = round(($suma_aux/$cant_aux)*100,2);
              if (($j%2) == 0)
                    $color = '#D0D0D0';
              else
                     $color = '#FFFFFF';
              $auxid=$t_serv+'v';

              $ori = str_pad($origen, 6 , "000000", STR_PAD_LEFT);
              $des = str_pad($destino, 6 , "000000", STR_PAD_LEFT);
              $srv = str_pad($servicio, 6 , "000000", STR_PAD_LEFT);
              $tipo = str_pad($t_serv, 6 , "000000", STR_PAD_LEFT);
              $tno = str_pad($turno, 6 , "000000", STR_PAD_LEFT);

              $tablaEntrada.="<tr style='cursor: pointer;' class='show' bgcolor='$color' id='".$fecha_diaria.$ori.$des.$srv.$tipo.$tno.$i_v."e'>
                              <td align='center'>$fecha_aux_completa</td>
                              <td align='right'>$cant_aux</td>
                              <td align='right'>$promedio</td>
                             </tr>";
              $j++;

              $datos.="[new Date($anio, $mes, $dia), $promedio]";
              if ($data)
                 $datos.=',';
        }
        $datos.="]";
       // print $datos;
       // die();
        $tablaEntrada.="</tbody></table>";
        mysql_close($conn);
        return array($datos, $tablaEntrada);

     }
?>
