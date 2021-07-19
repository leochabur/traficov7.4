<?

  session_start();
  date_default_timezone_set('America/Los_Angeles');
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];



  if($accion == 'hscond'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $table = generateTable($desde, $hasta, $_POST['str']);
     print $table;
  }
  
  function generateTable($desde, $hasta, $str){
           $sql = "select u.id, interno, date_format(fservicio,'%d/%m/%Y') as fecha, upper(razon_social) as cliente, upper(nombre) as orden, time_format(hsalida,'%H:%i') as hsalida, time_format(hllegada,'%H:%i') as hllegada, concat(fservicio,' ',hsalida) as sale, concat(fservicio,' ',hllegada) as llega
                   from ordenes o
                   inner join unidades u on u.id = o.id_micro
                   inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
                   where o.id_estructura = $str and fservicio between '$desde' and '$hasta' and not suspendida and not borrada
                   order by u.id, fservicio, hsalida";

           $tabla='<table id="example" name="example" align="center">';
           $conn = conexcion();
           $result = mysql_query($sql, $conn);
           $data = mysql_fetch_array($result);
           while ($data){
                 $datos='<thead>
                                 <tr class="ui-widget-header">
                                     <th colspan="5">'.$data[interno].'</th>
                                 </tr>
                                 <tr class="ui-widget-header">
                                     <th>Fecha Servicio</th>
                                     <th>Cliente</th>
                                     <th>Servicio</th>
                                     <th>Hora Salida</th>
                                     <th>Hora Llegada</th>
                                 </tr>
                          </thead>';
                 $coche = $data[0];
                 $last = array();
                 $super = false;
                 while(($data) && ($coche == $data[0])){
                               if (!empty($last)){
                                  $sale = new DateTime($data['sale']);
                                  $llega = new DateTime($last['llega']);
                                  if ($sale < $llega){
                                     $datos.="<tr>
                                                  <td>$last[fecha]</td>
                                                  <td>$last[cliente]</td>
                                                  <td>$last[orden]</td>
                                                  <td>$last[hsalida]</td>
                                                  <td>$last[hllegada]</td>
                                              </tr>";
                                     $datos.="<tr>
                                                  <td>$data[fecha]</td>
                                                  <td>$data[cliente]</td>
                                                  <td>$data[orden]</td>
                                                  <td>$data[hsalida]</td>
                                                  <td>$data[hllegada]</td>
                                              </tr>";
                                     $super=true;
                                  }
                               }
                               $last = $data;
                               $data = mysql_fetch_array($result);
                 }
                 $datos.="<tr><td colspan='5'><hr align='tr'><br></td></tr>";
                 if ($super){
                    $tabla.=$datos;
                 }
                 unset($last);

           }
           
           
           
     $tabla.='
              </table><style type="text/css">

                  </style>
                  <script type="text/javascript">
                  </script>  ';
     return $tabla;
  }
  
?>

