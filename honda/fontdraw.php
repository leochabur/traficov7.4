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
  $ce= substr($parama, 27, 1);
  if ($ce == 'c'){
  if ($iv == 'i'){
  $sql = "select date_format(o.fservicio, '%d/%m/%Y'),
                 upper(o.nombre),
                 interno,
                 date_format(o.hllegada,'%H:%i') as llegadiag,
                 date_format(o.hllegadaplantareal,'%H:%i') as llegareal,
                 round((if (o.hllegadaplantareal <= o.hllegada,1,
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hllegadaplantareal, o.hllegada))/60)-5)*0.06),
                               0))))*100,2) as porefic,
                 os.comentario
from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
left join unidades m ON (m.id = o.id_micro)
left join obsSupervisores os ON os.id_orden = o.id
where o.fservicio between '$desde' and '$hasta' and i_v = '$iv' and tu.id = $turno and o.id_cliente = 161 and not o.borrada and not o.suspendida
order by o.fservicio, o.nombre";

}
elseif($iv == 'v'){
  $sql = "select date_format(o.fservicio, '%d/%m/%Y'),
                 upper(o.nombre),
                 interno,
                 date_format(o.hsalida,'%H:%i') as saldiag,
                 date_format(o.hsalidaplantareal,'%H:%i') as salreal,
                 round((if (o.hsalidaplantareal <= o.hsalida,1,
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) <= 5,
                                       (1-(time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)*0.02),
                               if(
                                   (time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60) < 20,
                                       (0.9-((time_to_sec(timediff(o.hsalidaplantareal, o.hsalida))/60)-5)*0.06),
                               0))))*100,2) as porefic, os.comentario
from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
left join unidades m ON (m.id = o.id_micro)
left join obsSupervisores os ON os.id_orden = o.id
where o.fservicio between '$desde' and '$hasta' and i_v = '$iv' and tu.id = $turno and o.id_cliente = 161 and not o.borrada and not o.suspendida
order by o.fservicio, o.hsalida";
}
  }
  else{
  $sql = "select date_format(o.fservicio, '%d/%m/%Y'),
                 upper(o.nombre),
                 interno,
                 cantasientos as paxofrecidos,
                 cantpax as paxusados,
                 round((o.cantpax/cantasientos)*100,2) as porefic,
                 os.comentario
from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join turnos tu on tu.id = s.id_turno and tu.id_estructura = s.id_estructura_Turno
left join unidades m ON (m.id = o.id_micro)
left join obsSupervisores os ON os.id_orden = o.id
where o.fservicio between '$desde' and '$hasta' and i_v = '$iv' and tu.id = $turno and o.id_cliente = 161 and not o.borrada and not o.suspendida
order by o.fservicio, o.hsalida";
  }
  //die($sql);
      $conn = conexcion();
     $result = mysql_query($sql, $conn);
     if ($ce ==  'c'){
        if ($iv == 'i'){
           $header="<th>H. Llegada Diagramada</th>
                    <th>H. Llegada Real</th>
                    <th>% Confiabilidad</th>";
        }
        else{
           $header="<th>H. Salida Diagramada</th>
                    <th>H. Salida Real</th>
                    <th>% Confiabilidad</th>";
        }
     }
     elseif ($ce == 'e'){
           $header="<th>Plazas Ofrecidas</th>
                    <th>Plazas Utilizadas</th>
                    <th>% Eficiencia</th>";
     }
     
     $tabla.= "<table width='100%' class='order'>
                           <thead>

                                <tr>
                                    <th>Fecha</th>
                                    <th>Servicio</th>
                                    <th>Interno</th>
                                    $header
                                    <th>Observaciones</th>
                                </tr>
                           </thead>
                           <tbody>";
           $j=0;
           while ($data = mysql_fetch_array($result)){
                 if (($j%2) == 0)
                    $color = '#D0D0D0';
                 else
                     $color = '#FFFFFF';
                       $tabla.="<tr  bgcolor='$color' >
                                    <td  align='center'>$data[0]</td>
                                    <td  align='left'>$data[1]</td>
                                    <td align='center'>$data[2]</td>
                                    <td align='center'>$data[3]</td>
                                    <td  align='center'>$data[4]</td>
                                    <td  align='right'>$data[5]</td>
                                    <td  align='left'>$data[6]</td>
                                </tr>";
                       $j++;

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

