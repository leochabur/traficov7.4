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
  $destino = substr($parama, 16, 6);
  $servicio = substr($parama, 22, 6);
  $t_serv  = substr($parama, 28, 6);
  $turno= substr($parama, 34, 6);
  $i_v= substr($parama, 40, 1);
  $ce = substr($parama, 41, 1);

  $conn = conexcion();
        $calculo_conf = "cast(if (o.hllegada <= s.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, s.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, s.hllegada))/60)-5)*0.06),
                               0))) as decimal(5,2))";
        if (($i_v == 'v') || ($origen == 3)){
        $calculo_conf = "cast(if (o.hsalida <= s.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, s.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, s.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, s.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, s.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2))";
        }
        if ($servicio){
           $sql_srv = "select ciudades_id_origen from cronogramas c where id = $servicio";
           $res = mysql_query($sql_srv, $conn);
           if ($srv = mysql_fetch_array($res)){
              if ($srv[0] == 3){
                         $calculo_conf = "cast(if (o.hsalida <= s.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalida, s.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalida, s.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalida, s.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalida, s.hsalida))/60)-5)*0.06),
                               0))) as decimal(5,2))";
              }
           }
        }
        if ($servicio != 0)
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
        }

        $sql="SELECT date_format(fservicio, '%d/%m/%Y'),
                     upper(c.nombre),
                     interno,
                     s.hllegada,
                     s.hsalida,
                     o.hllegada,
                     o.hsalida,
                     $calculo_conf*100,
                     cantasientos,
                     cantpax,
                     round((cantpax/cantasientos)*100,2),
                     osu.comentario
                     
                 FROM (select * from ordenes where (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida) and (id_cliente = 10)) o
                 left join obsSupervisores osu ON osu.id = o.id
                 inner join unidades m ON (m.id = o.id_micro)
                 INNER JOIN servicios s ON s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                 INNER JOIN cronogramas c ON c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                 INNER JOIN tiposervicio ts ON ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
                 INNER JOIN turnos tu ON tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
                 INNER JOIN ciudades ori ON ori.id = c.ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen
                 INNER JOIN ciudades des ON des.id = c.ciudades_id_destino and des.id_estructura = c.ciudades_id_estructura_destino
                 $where
                 ORDER BY o.hcitacion";

     //  die($sql);

     $result = mysql_query($sql, $conn);
     if ($ce == 'c')
     $tabla.= "<table width='100%' class='order'>
                           <thead>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    <th>H. Llegada Diagramada</th>
                                    <th>H. Llegada Real</th>
                                    <th>% Confiabilidad</th>
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>";
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
                       if ($ce == 'c')
                          $tabla.="<td align='center'>$data[3]</td>
                                    <td  align='center'>$data[5]</td>
                                    <td  align='right'>$data[7]</td>";
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

