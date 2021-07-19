<?
     set_time_limit(0);
     error_reporting(0);
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
    include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../vista/paneles/viewpanel.php');
    
$accion = $_POST['accion'];
if ($accion == 'load'){

     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $sql = "SELECT fservicio, hsalida, nombre, razon_social,
        if (u.id is null, 'E1', interno) as interno, anio, ant_max,
        if (tu.id is null, 'E2', tipo) as tipo,
        if (rctu.id is null, 'E3', 1) as restTipo,
        if (rgc.id is null,
                         'E4',
                         if (filtralistaunidades,
                                    if (id_micro in (SELECT id_coche FROM restcochesclientes where id_cliente = o.id_cliente and id_estructuracliente = o.id_estructura_cliente),
                                        1,
                                         'E5'),
                             'NF')
        ) as restGral,
        if (rgc.id is null, 'E6', if (u.anio is null, 'E7', if((u.anio+ant_max) >= year(now()), 0, 1))) as ant
from ordenes o
left join unidades u on u.id = o.id_micro
left join tipounidad tu on tu.id = u.id_tipoUnidad and tu.id_estructura = u.id_estructura_tipounidad
left join (select * from restclientetipounidad where id_estructura = 1) rctu on rctu.id_cliente = o.id_cliente and rctu.id_estructuracliente = o.id_estructura_cliente and tu.id = rctu.id_tipounidad and tu.id_estructura = rctu.id_estructura_tipounidad
inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
left join restricciongralcliente rgc on rgc.id_cliente = o.id_cliente and rgc.id_estructuraCliente = o.id_estructura_cliente
where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and not borrada and not suspendida and not vacio";

       die($sql);
       $conn = conexcion();

       $result = mysql_query($sql, $conn);
       $tabla='<table id="example-advanced" name="example-advanced" border="1" widht="75%">
                      <tr class="ui-widget-header">
                          <th>Fecha Servicio</th>
                          <th>H. Salida</th>
                          <th>Cliente</th>
                          <th>Servicio</th>
                          <th>Interno</th>
                          <th></th>
                      </tr>

                    <tbody>';
       while ($data = mysql_fetch_array($result)){
             $ok = false;
             if ($data[restGral] == 'E4'){
                $obs = "No se han generado restricciones para el cliente.";
                $ok = true;
             }
             elseif($data[restGral] == 'E5'){
                $obs = "La unidad no se encuentra dentro dentro de las admitidas por el cliente";
                $ok = true;
             }
             elseif($data[restGral] == 'E7'){
                $obs = "El interno no se le ha cargado el año";
                $ok = true;
             }
             elseif(!($data[restGral] == 0)){
                $obs = "El interno supera la antiguedad permitida por el cliente";
                $ok = true;
             }
             elseif ($data[interno] == 'E1'){
                $obs = "Orden sin interno asignado";
                $ok = true;
             }
             elseif($data[tipo] == 'E2'){
                $obs = "Interno sin tipo de unidad asignada";
                $ok = true;
             }
             if ($ok){
                      $tabla.="<tr class='ui-widget-header'>
                                   <td>$data[0]</td>
                                   <td>$data[1]</td>
                                   <td>".htmlentities($data[2])."</td>
                                   <td>".htmlentities($data[3])."</td>
                                   <td>$data[4]</td>
                                   <td>$data[5]</td>
                                   <td>$data[6]</td>
                                   <td>$obs</td>
                      </tr>";
             }
       }
       $tabla.="</tbody></thead><script>
                                        $('#example-advanced').treetable();
                                        });</script>";
       mysql_close($conn);
       print $tabla;
}

function cochesHab($conn, $str){ ////obtiene listado coches habilitados
         $sql = "SELECT id_cliente, id_coche
                 FROM restcochesclientes r
                 where id_estructuracliente = $str
                 order by id_cliente";
         $coches = array();
         $result = mysql_query($sql, $conn);
         $data = mysql_fetch_array($result);
         while ($data){
               $cliente = $data[0];
               $coches[$data[0]] = array();
               while (($data) && ($cliente == $data[0])){
                     $coches[$data[0]][$data[1]]=1;
                     $data = mysql_fetch_array($result);
               }
         }
         return $coches;
}
?>

