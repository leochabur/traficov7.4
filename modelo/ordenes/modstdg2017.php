<?php
  set_time_limit(0);
  session_start();

  ////////////////// modulo para dar de alta Ciudades /////////////////////

  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion = $_POST['accion'];
  $estado = $_POST['estados'];
  
  if ($accion == 'sve'){ ///codigo para guardar ////
        $ok=1;
        $fecha = dateToMysql($_POST['fecha'], '/');
        $conn = conexcion();

        
		$sql="DELETE FROM estadoDiagramasDiarios WHERE (fecha ='$fecha') and (id_estructura = $_SESSION[structure])";
		$resul = mysql_query($sql, $conn);

        $sql = "update ordenes set hcitacionreal = hcitacion, hsalidaplantareal = hsalida, hllegadaplantareal = hllegada, hfinservicioreal = hfinservicio WHERE (fservicio ='$fecha') and (id_estructura = $_SESSION[structure])";
        $result = mysql_query($sql, $conn);
        
        $sql="INSERT INTO estadoDiagramasDiarios (id_estado, fecha, finalizado, usuario, fechahorafinalizacion, id_estructura) values ($_POST[estados], '$fecha', $estado, $_SESSION[userid] , now(), $_SESSION[structure])";
        $resul = mysql_query($sql, $conn);
        mysql_close($conn);
        print json_encode($ok);
  }
  elseif($accion == 'vdga'){
        $fecha = dateToMysql($_POST['fechav'], '/');
        $conn = conexcion();
        $sql = "Select upper(razon_social) as nombre, upper(c.nombre) as servicio, date_format(hcitacion, '%H:%i') as cita, date_format(hsalida, '%H:%i') as sale,
                       date_format(hllegada, '%H:%i') as llega
                FROM servicioscontroldiagrama scd
                inner join servicios s on s.id = scd.id_servicio
                inner join cronogramas c on c.id = s.id_cronograma
                inner join clientes cl on cl.id = c.id_cliente
                where (scd.id_controlDiagrama = $_POST[ctrl]) and (scd.id_servicio not in (select id_servicio from ordenes where fservicio = '$fecha' and id_estructura = $_SESSION[structure]))";

        $tabla='<div id="tabs">
                     <ul>
                         <li><a href="#tabs-1">Faltante de diagramar</a></li>
                         <li><a href="#tabs-2">Diagramado en exceso</a></li>
                     </ul>
                     <div id="tabs-1">
                          <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                                 <thead>
                                        <tr class="ui-widget-header">
                                        <th>Cliente</th>
                        <th>Servicio</th>
                        <th>H. Citacion</th>
                        <th>H. Salida</th>
                        <th>H. Llegada</th>
                    </tr>
                    </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[2]</td>
                           <td>$row[3]</td>
                           <td>$row[4]</td>
                       </tr>";
        }
        $tabla.='</tbody>
                 </table>
                 </div>';
        $sql="Select upper(razon_social) as nombre, upper(c.nombre) as servicio, date_format(o.hcitacion, '%H:%i') as cita, date_format(o.hsalida, '%H:%i') as sale,
                     date_format(o.hllegada, '%H:%i') as llega, interno, concat(if(o.id_chofer_1 is null, '', e1.apellido),' / ',if(o.id_chofer_2 is null,'',e2.apellido)) as conductor
              FROM  ordenes o
              inner join servicios s on s.id = o.id_servicio
              inner join cronogramas c on c.id = s.id_cronograma
              inner join clientes cl on cl.id = c.id_cliente
              left join unidades u on u.id = o.id_micro
              left join empleados e1 on e1.id_empleado = o.id_chofer_1
              left join empleados e2 on e2.id_empleado = o.id_chofer_2
              where (fservicio = '$fecha')and (not borrada) and (not suspendida) and (o.id_servicio not in (select id_servicio from servicioscontroldiagrama where id_controlDiagrama = $_POST[ctrl])) and o.id_estructura = $_SESSION[structure]";
        $tabla.='<div id="tabs-2">
                 <table id="example_out" name="example_out" class="ui-widget ui-widget-content" width="100%" align="center">
                        <thead>
                               <tr class="ui-widget-header">
                                        <th>Cliente</th>
                                        <th>Servicio</th>
                                        <th>H. Salida</th>
                                        <th>H. Llegada</th>
                                        <th>Conductores</th>
                                        <th>Interno</th>
                                        </tr>
                    </thead>
                    <tbody>';
        $result = ejecutarSQL($sql, $conn);
        while ($row = mysql_fetch_array($result)){
              $tabla.="<tr>
                           <td>".htmlentities($row[0])."</td>
                           <td>".htmlentities($row[1])."</td>
                           <td>$row[3]</td>
                           <td>$row[4]</td>
                           <td>$row[6]</td>
                           <td>$row[5]</td>
                       </tr>";
        }
                 
        $tabla.='</tbody>
                 </table>
                 </div>
                 </div>';
        
        
        $tabla.='
<style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {

                                        background-color: #FF8080;
                                        }
                         #example_out { font-size: 85%; }
                         #example_out tbody tr:hover {

                                        background-color: #FF8080;
                                        }

                         #example tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                         #example_out tr:nth-child(odd) {
                                           background-color:#f2f2f2;
                                           }
                         #example_out tr:nth-child(even) {
                                            background-color:#fbfbfb;
                                            }
                  </style>
        <script>
                              $( "#tabs" ).tabs();
                    </script>';
        print $tabla;








  }
?>

