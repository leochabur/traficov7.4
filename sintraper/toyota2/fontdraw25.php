<?php
  session_start();

  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('./bdadmin.php');


  $parama=$_POST['params'];

  $desde= substr($parama, 1, 10);
  $hasta= substr($parama, 12, 10);
  $turno= substr($parama, 23, 1);
  $t_ser= substr($parama, 25, 1);
  $iv= substr($parama, 26, 1);

  $sql = "select date_format(fservicio, '%d/%m/%Y'), upper(nombre), date_format(s.hsalida,'%H:%i') as saldiag, date_format(s.hllegada,'%H:%i') as llegadiag, date_format(o.hsalida,'%H:%i') as salreal, date_format(o.hllegada,'%H:%i') as llegareal, round((if (o.hllegada <= s.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegada, s.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegada, s.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegada, s.hllegada))/60)-5)*0.06),
                               0))))*100,2) as porefic, interno, cantasientos as paxofrecidos, cantpax as paxusados, round((cantpax/cantasientos)*100,2) as porefic, concat(tipo,' ' ,turno,' ',i_v)
from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join tiposervicio ts on ts.id = s.id_TipoServicio and ts.id_estructura = s.id_estructura_TipoServicio
inner join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
inner join unidades m ON (m.id = o.id_micro)
where fservicio between '$desde' and '$hasta' and ts.id = $t_ser and i_v = '$iv' and tu.id = $turno and o.id_cliente = 10 and not borrada and not suspendida
order by fservicio, o.hsalida";

      $conn = conexcion();
     $result = mysql_query($sql, $conn);
     $tabla.= "<table width='100%' class='order'>
                           <thead>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Servicio</th>
                                    <th>H. Salida Diagramada</th>
                                    <th>H. Llegada Diagramada</th>
                                    <th>H. Salida Real</th>
                                    <th>H. Llegada Real</th>
                                    <th>% Confiabilidad</th>
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
                                    <td align='center'>$data[2]</td>
                                    <td align='center'>$data[3]</td>
                                    <td  align='center'>$data[4]</td>
                                    <td  align='center'>$data[5]</td>
                                    <td  align='right'>$data[6]</td>
                                    <td  align='right'>$data[7]</td>
                                    <td  align='right'>$data[8]</td>
                                    <td align='right'>$data[9]</td>
                                    <td align='right'>$data[10]</td>
                                    <td  align='right'>$data[13]</td>
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

