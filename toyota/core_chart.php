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
                                               vAxis: {
                                                      viewWindowMode: 'explicit',
                                                      viewWindow: {
                                                                  max: 100,
                                                                  min:0
                                                                  }
                                               },
                                               pointSize: 15
                                               };
                                   var chart = new google.visualization.LineChart(document.getElementById('$nomDiv'));
                                   chart.draw(datavta, options);
                                   google.visualization.events.addListener(chart, 'select', esportar);
                                   function esportar(e) {
                                                        var xxx = chart.getSelection()[0];
                                   }
                          }";
              return $function;
     }

     function titulo($servicio,$origen,$destino, $avanzada, $i_v, $fil_srv, $fil_city, $tipo, $turno){
          // if (($servicio) || ($origen) || ($destino)){ //si se filtra por algun otro filtro origen/destino/servicio solo genera un grafico
        $conn = conexcion();
        
             if ($tipo == '2')
                $titulo = "PRODUCCION";
             elseif ($tipo == '1')
                $titulo = "ADMINISTRACION";
             else
                $titulo = "MANTENIMIENTO";

             if ($turno == '1')
                $titulo.= " - TURNO MAÑANA";
             elseif ($turno == '2')
                $titulo.= " - TURNO TARDE";
             else
                $titulo.= " - TURNO NOCHE";
                if ($i_v == 'iv')
                   $titulo.=" (ENTRADA - SALIDA DE PLANTA)";
                elseif($i_v == 'i')
                   $titulo.=" (ENTRADA A PLANTA)";
                else
                    $titulo.=" (SALIDA DE PLANTA)";
                    
        if ($avanzada){
           if ($fil_srv){
              $result = mysql_query("SELECT upper(nombre) FROM cronogramas WHERE id = $servicio", $conn);
              if ($data = mysql_fetch_array($result))
                 $titulo = $data[0];
           }
           else{
               // die("SELECT upper(ciudad) FROM ciudades WHERE id = $origen  $avanzada  $fil_city");
                if ($fil_city){
              //     die("SELECT upper(ciudad) FROM ciudades WHERE id = $origen");
                   $result = mysql_query("SELECT upper(ciudad) FROM ciudades WHERE id = $origen", $conn);
                   if ($data = mysql_fetch_array($result))
                      $titulo= $data[0]." $titulo";
                }
           }
        }

           return utf8_decode($titulo);
     }
     //id, id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km, id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, id_chofer_1, id_estructura_chofer1, finalizada, id_chofer_2, id_estructura_chofer2, borrada, comentario, id_micro, vacio, id_user, fecha_accion, cantpax, suspendida, checkeada, id_claseservicio, id_estructuraclaseservicio, peajes,

     function graphConfiabilidad($fdesde, $fhasta, $origen, $destino, $servicio, $t_serv, $turno, $i_v ,$avanzada, $fil_city, $city_origen, $iv, $fil_srv, $servicio){
        $desde = dateToMysql($fdesde, '/');
        $hasta = dateToMysql($fhasta, '/');
        $conn = conexcion();
        $estructura = 1;            //hllegadaplantareal, hsalidaplantareal, hfinservicioreal, hcitacionreal
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
        

        $sql="SELECT date_format(o.fservicio, '%d'), $calculo_conf, date_format(o.fservicio, '%d/%m/%Y'), o.fservicio as auxi, o.fservicio, day(o.fservicio), (month(o.fservicio)-1), year(o.fservicio)
              FROM (select id_estructura, id_servicio, id_estructura_servicio, hsalidaplantareal, hllegadaplantareal, hsalida, hllegada, fservicio, id from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o";
        $group_by="ORDER BY fservicio";
        
        if ($avanzada){
           if ($fil_srv){
              $sql.= " INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma from servicios where id_cronograma = $servicio and id_estructura_cronograma = $estructura) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";
           }
           else{
                $filtro_ciudad = "";
                if ($fil_city){
                   $cond_city = " ciudades_id_origen = $city_origen and";
                   if ($i_v == 'v'){
                      $cond_city = " ciudades_id_destino = $city_origen and";
                   }
                   $filtro_ciudad = " INNER JOIN (select id, id_estructura from cronogramas where $cond_city id_cliente = 10) c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma ";
                }
                else
                    if ($i_v == 'iv')
                       $filtro_iv = "";
                    else
                        $filtro_iv = " and i_v = '$i_v' ";
                $sql.=" INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma from servicios where id_TipoServicio = $t_serv and id_turno = $turno $filtro_iv) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio $filtro_ciudad";
           }
        }
        else{
             $sql.=" INNER JOIN (select id, id_estructura from servicios where id_TipoServicio = $t_serv and id_turno = $turno and i_v = '$i_v') s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";
        
        }
        
     //   die($sql);

        $sql.=" $group_by";
       // print $sql;
      //  die($sql);


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
              
              $tablaEntrada.="<tr style='cursor: pointer;' class='show' bgcolor='$color' id='".$fecha_diaria.$ori.$des.$srv.$tipo.$tno.$i_v."c".$avanzada.$fil_srv.$fil_city.$city_origen."'>
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
     
function graphEficiencia($fdesde, $fhasta, $origen, $destino, $servicio, $t_serv, $turno, $i_v, $avanzada, $fil_city, $city_origen, $iv, $fil_srv, $servicio){
        $desde = dateToMysql($fdesde, '/');
        $hasta = dateToMysql($fhasta, '/');
        $estructura = 1;
        $conn = conexcion();
        //estructura basica para todas las consultas independiente de los filtros aplicados
        $calculo_conf = "(cantpax/cantasientos)";

        $sql="SELECT date_format(o.fservicio, '%d'), $calculo_conf, date_format(o.fservicio, '%d/%m/%Y'), o.fservicio as auxi, o.fservicio, day(o.fservicio), (month(o.fservicio)-1), year(o.fservicio)
              FROM (select cantpax, id_micro, id_estructura, id_servicio, id_estructura_servicio, hsalida, hllegada, fservicio, id from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o
              LEFT JOIN unidades u on u.id = o.id_micro ";
        $group_by="ORDER BY fservicio";

        if ($avanzada){
           if ($fil_srv){
              $sql.= " INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma from servicios where id_cronograma = $servicio and id_estructura_cronograma = $estructura) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";
           }
           else{
                $filtro_ciudad = "";
                $cond_city = " ciudades_id_origen = $city_origen ";
                if ($fil_city){
                   $filtro_iv= "";
                   if ($i_v == 'v'){
                      $cond_city = " ciudades_id_destino = $city_origen ";
                   }
                   $filtro_ciudad = " INNER JOIN (select id, id_estructura from cronogramas where $cond_city and id_cliente = 10) c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma ";
                }else{
                    if ($i_v == 'iv'){
                       $filtro_iv= "";
                    }else{
                        $filtro_iv= " and i_v = '$i_v' ";
                    }
                }
                $sql.=" INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma from servicios where id_TipoServicio = $t_serv and id_turno = $turno $filtro_iv) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio $filtro_ciudad";
           }
        }
        else{
             $sql.=" INNER JOIN (select id, id_estructura from servicios where id_TipoServicio = $t_serv and id_turno = $turno and i_v = '$i_v') s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";

        }

        $sql.=" $group_by";
       // print $sql;
      //  die($sql);


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

              $tablaEntrada.="<tr style='cursor: pointer;' class='show' bgcolor='$color' id='".$fecha_diaria.$ori.$des.$srv.$tipo.$tno.$i_v."e".$avanzada.$fil_srv.$fil_city.$city_origen."'>
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
