<?php
  session_start();

  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('./bdadmin.php');


  $parama=$_POST['params'];

  $desde= substr($parama, 0, 10);
  $hasta= $desde;
  
  $origen = substr($parama, 10, 6);
  $city_origen = $origen;
  $destino = substr($parama, 16, 6);
  $servicio = substr($parama, 22, 6);
  $t_serv  = substr($parama, 28, 6);
  $turno= substr($parama, 34, 6);
  $i_v= substr($parama, 40, 1);
 // die($i_v);
  $ce = substr($parama, 41, 1);
  $avanzada = substr($parama, 42, 1);
  $fil_srv = substr($parama, 43, 1);
  $fil_city = substr($parama, 44, 1);
  $estructura = 1;
  $conn = conexcion();
        $calculo_conf = "cast(if (o.hllegada <= ho.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, ho.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, ho.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, ho.hllegada))/60)-5)*0.06),
                               0))) as decimal(5,2))";
        if (($i_v == 'v')){
        $calculo_conf = "cast(if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2))";
        }
    /*    if ($servicio){
           $sql_srv = "select ciudades_id_origen from cronogramas c where id = $servicio";
           $res = mysql_query($sql_srv, $conn);
           if ($srv = mysql_fetch_array($res)){
              if ($srv[0] == 3){
                         $calculo_conf = "cast(if (o.hsalida <= ho.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, ho.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, ho.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, ho.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2))";
              }
           }
        }      */
      /*  if ($servicio != 0)
           $where = " WHERE c.id = $servicio";

        if ($origen != 0)
           $where = "WHERE ori.id = $origen";

        if ($destino != 0)
           if ($origen)
              $where = "WHERE ori.id = $origen and des.id = $destino";
           else
               $where = "WHERE des.id = $destino";
        if (($servicio==0) && ($origen==0) && ($destino==0)){
           $where="WHERE ts.id = $t_serv and tu.id = $turno and i_v ='$i_v'";
        }    */

        $sql="SELECT date_format(fservicio, '%d/%m/%Y'),
                     upper(o.nombre),
                     interno,
                     ho.hllegada,
                     ho.hsalida,
                     o.hllegada,
                     o.hsalida,
                     $calculo_conf*100,
                     cantasientos,
                     cantpax,
                     round((cantpax/cantasientos)*100,2),
                     osu.comentario,
                     o.fservicio ";
                     
           /*      "FROM (select * from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o
                 left join obsSupervisores osu ON osu.id = o.id
                 inner join unidades m ON (m.id = o.id_micro)
                 INNER JOIN (select id_estructura, id_servicio, id_estructura_servicio, hsalida, hllegada, id from horarios_ordenes) ho on ho.id = o.id
                 INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma, id_TipoServicio, id_turno, id_estructura_Turno,id_estructura_TipoServicio from servicios where activo) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 INNER JOIN cronogramas c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                 INNER JOIN tiposervicio ts ON ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 INNER JOIN turnos tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 INNER JOIN ciudades ori ON ori.id = c.ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen
                 INNER JOIN ciudades des ON des.id = c.ciudades_id_destino and des.id_estructura = c.ciudades_id_estructura_destino
                 $where
                 ORDER BY o.hcitacion";    */

        $sql.=" FROM (select id_estructura, id_micro, nombre, cantpax, id_servicio, id_estructura_servicio, hsalida, hllegada, fservicio, id from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o
              INNER JOIN (select id_estructura, id_servicio, id_estructura_servicio, hsalida, hllegada, id from horarios_ordenes) ho on ho.id = o.id
              left join obsSupervisores osu ON osu.id = o.id
              left join unidades m ON (m.id = o.id_micro) ";
        $group_by="ORDER BY fservicio";

     //   die($avanzada);
        if ($avanzada){
           if ($fil_srv){
              $sql.= " INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma from servicios where activo and id_cronograma = $servicio and id_estructura_cronograma = $estructura) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";
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
               // die("filtro $filtro_iv $i_v");
                $sql.=" INNER JOIN (select id, id_estructura, id_cronograma, id_estructura_cronograma  from servicios where activo and id_TipoServicio = $t_serv and id_turno = $turno $filtro_iv) s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio $filtro_ciudad";
           }
        }
        else{
             $sql.=" INNER JOIN (select id, id_estructura from servicios where activo and id_TipoServicio = $t_serv and id_turno = $turno and i_v = '$i_v') s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio ";

        }
     $sql.=" $group_by";
   //  die($sql);
     $result = mysql_query($sql, $conn);
     if ($ce == 'c'){
     $tabla.= "<table width='100%' class='order'>
                           <thead>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>";
                                if ($i_v == 'i')
                                    $tabla.="<th>H. Llegada Diagramada</th>
                                    <th>H. Llegada Real</th>";
                                else
                                    $tabla.="<th>H. Salida Diagramada</th>
                                    <th>H. Salida Real</th>";
                                $tabla.="<th>% Confiabilidad</th>
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>";
}
else
     $tabla.= "<table width='100%' class='order'>
                           <thead>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>Plazas Ofrecidas</th>
                                    <th>Plazas Utilizadas</th>
                                    <th>% Eficiencia</th>
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>";
           while ($data = mysql_fetch_array($result)){
                       $tabla.="<tr>
                                    <td  align='center'>$data[0]</td>
                                    <td  align='left'>$data[1]</td>
                                    <td align='center'>$data[2]</td>";
                       if ($ce == 'c'){
                          if ($i_v == 'i')
                          $tabla.="<td align='center'>$data[3]</td>
                                    <td  align='center'>$data[5]</td>
                                    <td  align='right'>$data[7]</td>";
                          elseif ($i_v == 'v')
                          $tabla.="<td align='center'>$data[4]</td>
                                    <td  align='center'>$data[6]</td>
                                    <td  align='right'>$data[7]</td>";
                       }
                       else
                          $tabla.="<td  align='right'>$data[8]</td>
                                    <td  align='right'>$data[9]</td>
                                    <td align='right'>$data[10]</td>";
                       $tabla.="<td  align='right'>$data[11]</td>
                                </tr>";

           }
           
           $tabla.="</tbody></table><br>";

     $tabla.="<style type='text/css'>
                     table.order {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 7pt;
	                              text-align: left;
                               }
                     table.order thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.order tbody td {
	                                        color: #3D3D3D;
	                                        padding: 4px;
	                                        vertical-align: top;
                                         }
                     td.click, th.click{
                                        background-color: #bbb;
                                        }
                     td.hover, tr.hover{
                                        background-color: #69f;
                                        }
                     th.hover, tfoot td.hover{
                                              background-color: ivory;
                                              }
                     td.hovercell, th.hovercell{
                                                background-color: #abc;
                                                }
                     td.hoverrow, th.hoverrow{
                                              background-color: #6df;
                                              }
              </style>
               <script type='text/javascript'>
                                $('.order').tableHover();
               </script>";
     print $tabla;

  
?>

