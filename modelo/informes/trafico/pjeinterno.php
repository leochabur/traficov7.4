<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if($accion == 'reskm'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');


     $sql = "select interno, tipo, ep.lugar, count(*), round(precio_peaje,2), round((count(*)*precio_peaje),2)
              from ordenes o
              inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
              inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
              inner join unidades u on u.id = o.id_micro
              inner join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
              inner join peajesporcronogramas pxc on c.id = pxc.id_cronograma and c.id_estructura = pxc.id_estructura_cronograma
              inner join estacionespeaje ep on ep.id = pxc.id_estacion_peaje and ep.id_estructura = pxc.id_estructura_estacion_peaje
              inner join preciopeajeunidad ppu on ppu.id_estacionpeaje = ep.id and ppu.id_estructura_estacionpeaje = ep.id_estructura and ppu.id_tipounidad = tu.id and  ppu.id_estructura_tipounidad = tu.id_estructura
              where (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_SESSION[structure]) and (not borrada) and (not suspendida)
              group by u.interno, ep.id
              order by interno";           

     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla.='<table id="example" name="example" class="table table-zebra" width="75%" align="center">
              <thead>
                  <tr>
                      <th>Interno</th>
                      <th>Tipo Vehiculo</th>
                      <th>Estacion Peajes</th>
                      <th>Cant. Pasadas</th>
                      <th>Precio Unitario</th>
                      <th>Precio Total</th>

                  </tr>
              </thead>
              <tbody>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<tr>
                       <td align='left'>$data[0]</td>
                       <td align='left'>$data[1]</td>
                       <td align='left'>$data[2]</td>
                       <td align='right'>$data[3]</td>
                       <td align='right'>$data[4]</td>       
                       <td align='right'>$data[5]</td>                
                  </tr>";
     }

     $tabla.='</tbody>
              </table>';
    print $tabla;
  }
  
?>

