<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'mens'){
     $conn = conexcion();
     $rango = "$_POST[desde]  -  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     
     $resumen = resumenPorEstado($conn, $desde, $hasta);
     ksort($resumen);

     $sql = "SELECT count(*)
             FROM siniestros s
             WHERE fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica";
  //   die($sql);
     $result = mysql_query($sql, $conn);
     if ($data = mysql_fetch_array($result)){
        $tot_sin = $data[0];
     }
     
     $sql = "SELECT count(*) as indice
             FROM siniestros s
             WHERE fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica and tipo_lesion in (4,5)";

     $sql = "SELECT sum(km) as km
             FROM ordenes o
             WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida and id_estructura = 1";
             
     $result = mysql_query($sql, $conn);
     if ($data = mysql_fetch_array($result)){
        $km = $data[0];
     }

     $sql = "SELECT count(*) as km
             FROM ordenes o
             WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida and id_estructura = 1";
     $result = mysql_query($sql, $conn);
     if ($data = mysql_fetch_array($result)){
        $count = $data[0];
     }
             
     $tabla='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                   <legend class="ui-widget ui-widget-header ui-corner-all">1. SINTESIS</legend>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">1. Resumen Gral</legend>
		                 <table border="1">
                                <thead>
                                       <tr>
                                           <th>Periodo</th>
                                           <th>Siniestros</th>
                                           <th>KM Recorridos</th>
                                           <th>Cant. Servicios</th>
                                       </tr>
                                </thead>
                                <tbody>
                                       <tr>
                                           <td align="center">'.$rango.'</td>
                                           <td align="right">'.$tot_sin.'</td>
                                           <td align="right">'.$km.'</td>
                                           <td align="right">'.$count.'</td>
                                       </tr>
                                </tbody>
		                 </table>
		                 </fieldset>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">1. Resumen Por Estado</legend>
		                 <table border="1">
                                <thead>
                                       <tr>
                                           <th>Estado siniestros</th>
                                           <th>Acumulado al inicio</th>
                                           <th>Del Periodo</th>
                                           <th>Total</th>
                                       </tr>
                                </thead>
                                <tbody>';
                                       foreach ($resumen as $clave => $valor){

                                           $tabla.="<tr><td align='center'>$clave</td>
                                           <td align='right'>$valor[0]</td>
                                           <td align='right'>$valor[1]</td>
                                           <td align='right'>".($valor[0]+$valor[1])."</td></tr>";
                                       }
                                       $tabla.='
                                </tbody>
		                 </table>
		                 </fieldset>
                   </fieldset>';
                   
     $sql="SELECT tipo, count(*) as actual, (SELECT count(*)
                        FROM siniestros
                        where tipo_lesion = s.tipo_lesion and fecha_siniestro between DATE_ADD('$desde', INTERVAL -1 YEAR) and DATE_ADD('$hasta', INTERVAL -1 YEAR)) as anterior,
                        round(count(*)/12, 2) as media_mensual
           FROM siniestros s
           inner join tipoLesionSiniestro tl on tl.id = s.tipo_lesion
           where fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica
           group by tipo_lesion";
      //     die($sql);
     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">2. CONSECUENCIAS</legend>
                         <table border="1" width="">
                                <tr>
                                    <th rowspan="2">Consecuencias</th>
                                    <th rowspan="2">Periodo Actual</th>
                                    <th colspan="2">Periodo Anterior</th>
                                </tr>
                                <tr>
                                    <td>Mismo Mes</td>
                                    <td>Media Mensual</td>
                                </tr>';
     $sql = "SELECT * FROM tipoLesionSiniestro t order by tipo";
     $result = mysql_query($sql, $conn);
     while ($data = mysql_fetch_array($result)){
           $sql="SELECT count(*) as actual,
      (SELECT count(*)
       FROM siniestros where tipo_lesion = s.tipo_lesion and
            fecha_siniestro between DATE_ADD('2016-11-01', INTERVAL -1 YEAR) and DATE_ADD('2016-12-31', INTERVAL -1 YEAR)) as anterior,
       round(count(*)/12, 2) as media_mensual
FROM siniestros s
where fecha_siniestro between '$desde' and '$hasta' and not borrada and s.tipo_lesion = $data[0] and afecta_estadistica
group by tipo_lesion";
         $res = mysql_query($sql, $conn);
         if ($row = mysql_fetch_array($res)){
         }
        $tabla.="<tr>
                     <td align='left'>$data[1]</td>
                     <td align='right'>$row[0]</td>
                     <td align='right'>$row[1]</td>
                     <td align='right'>$row[2]</td>
                 </tr>";
  }
$tabla.='</table>
                         </fieldset>';
     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">3. COSTOS ESTIMADOS</legend>
                         </fieldset>';
     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">4. CAUSAS FUENTES</legend>
                                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                                   <legend class="ui-widget ui-widget-header ui-corner-all">4.1 RESPONSABILIDAD</legend>
                                           <table border="1" width="">
                                                  <tr>
                                                      <th>Responsabilidad</th>
                                                      <th>Cantidad</th>
                                                      <th>Conductores</th>
                                                  </tr>';
                                           $sql="SELECT * FROM resp_estimada_siniestro order by responsabilidad";
                                           $result = mysql_query($sql, $conn);
                                           while ($data = mysql_fetch_array($result)){
                                                 $sql = "SELECT upper(apellido)
                                                         FROM siniestros s
                                                         inner join empleados e on e.id_empleado = s.id_empleado
                                                         where fecha_siniestro between '$desde' and '$hasta' and not borrada and resp_estimada = $data[0]";
                                                 $res = mysql_query($sql, $conn);
                                                 $cant=0;
                                                 $cnd="";
                                                 while ($row = mysql_fetch_array($res)){
                                                       $cant++;
                                                       if ($data[0] == 1)
                                                          $cnd.="$row[0], ";
                                                 
                                                 }
                                                 $tabla.="<tr>
                                                              <td>$data[1]</td>
                                                              <td>$cant</td>
                                                              <td>$cnd</td>
                                                          </tr>";
                                           }
		                                   
     $tabla.='</table></fieldset>
                                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                                   <legend class="ui-widget ui-widget-header ui-corner-all">4.2 NORMA NO RESPETADA</legend>
		                                   <table border="1" width="">
                                                  <tr>
                                                      <th>N'.htmlentities('°').'</th>
                                                      <th>Norma no respetada</th>
                                                      <th>Cantidad</th>
                                                  </tr>';
                                           $sql="SELECT * FROM normas_seg_vial ";
                                           $result = mysql_query($sql, $conn);
                                           while ($data = mysql_fetch_array($result)){
                                                 $sql = "SELECT count(*)
                                                         FROM siniestros s
                                                         where fecha_siniestro between '$desde' and '$hasta' and not borrada and norma_no_respetada = $data[0]";
                                                 $res = mysql_query($sql, $conn);
                                                 if ($row = mysql_fetch_array($res)){

                                                 }
                                                 $tabla.="<tr>
                                                              <td>$data[0]</td>
                                                              <td>$data[1]</td>
                                                              <td align='right'>$row[0]</td>
                                                          </tr>";
                                           }
     $tabla.='</table>
                                 </fieldset>
                                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                                   <legend class="ui-widget ui-widget-header ui-corner-all">4.3 ACCIONES CORRECTIVAS</legend>
                                 </fieldset>
                         </fieldset>';
    print $tabla;
  }
  
  function resumenPorEstado($conn, $desde, $hasta){
           $sql = "select estado, count(*) as cant, 1 as delperiodo
from (SELECT id, upper(estado) as estado
            FROM estadosiniestro e
            union all
            select 0 as id, 'NO DETALLA' as estado) e
left join  (select if (s.id_estado is null, 0, id_estado) as id_estado, fecha_siniestro
      from siniestros s
      where not borrada and afecta_estadistica) s on s.id_estado = e.id
where fecha_siniestro between '$desde' and '$hasta'
group by id_estado
union all
select estado, count(*) as cant, 0 as delperiodo
from (SELECT id, upper(estado) as estado
            FROM estadosiniestro e
            union all
            select 0 as id, 'NO DETALLA' as estado) e
left join  (select if (s.id_estado is null, 0, id_estado) as id_estado, fecha_siniestro
      from siniestros s
      where not borrada and afecta_estadistica) s on s.id_estado = e.id
where fecha_siniestro < '$desde'
group by id_estado";
     // die($sql);
      $result = mysql_query($sql, $conn);
      $resumen = array();
      while ($data = mysql_fetch_array($result)){
            $resumen[$data[estado]][$data[delperiodo]] = $data[cant];
      }
      
        return $resumen;
  }

  
?>

