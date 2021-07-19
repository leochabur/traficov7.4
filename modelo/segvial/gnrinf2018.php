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
     
     $str="";
     if ($_POST[str])
        $str="and id_estructura = $_POST[str]";
        
     $resumen = resumenPorEstado($conn, $desde, $hasta, $_POST[str]);
     ksort($resumen);

     $sql = "SELECT count(*)
             FROM siniestros s
             WHERE fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica $str";
  //   die($sql);
     $result = mysql_query($sql, $conn);
     if ($data = mysql_fetch_array($result)){
        $tot_sin = $data[0];
     }
     
     $sql = "SELECT count(*) as indice
             FROM siniestros s
             WHERE fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica and tipo_lesion in (4,5) $str";

     $sql = "SELECT sum(km) as km
             FROM ordenes o
             WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida $str";
             
     $result = mysql_query($sql, $conn) or die($sql);
     if ($data = mysql_fetch_array($result)){
        $km = $data[0];
     }

     $sql = "SELECT count(*) as km
             FROM ordenes o
             WHERE fservicio between '$desde' and '$hasta' and not borrada and not suspendida $str";
     $result = mysql_query($sql, $conn);
     if ($data = mysql_fetch_array($result)){
        $count = $data[0];
     }
             
     $tabla='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                   <legend class="ui-widget ui-widget-header ui-corner-all">1. SINTESIS</legend>
		                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">1.1 Resumen Gral</legend>
		                 <table class="table table-zebra">
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
		                 <legend class="ui-widget ui-widget-header ui-corner-all">1.2 Resumen Por Estado</legend>
		                 <table class="table table-zebra">
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
                        where tipo_lesion = s.tipo_lesion and fecha_siniestro between DATE_ADD('$desde', INTERVAL -1 YEAR) and DATE_ADD('$hasta', INTERVAL -1 YEAR) $str) as anterior,
                        round(count(*)/12, 2) as media_mensual
           FROM siniestros s
           inner join tipoLesionSiniestro tl on tl.id = s.tipo_lesion
           where fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica $str
           group by tipo_lesion";
      //     die($sql);
     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">2. CONSECUENCIAS</legend>
                         <table class="table table-zebra">
                                <thead>
                                <tr>
                                    <th rowspan="2">Consecuencias</th>
                                    <th rowspan="2">Periodo Actual</th>
                                    <th colspan="2">Periodo Anterior</th>
                                </tr>
                                <tr>
                                    <th>Mismo Mes</th>
                                    <th>Media Mensual</th>
                                </tr>
                                </thead>
                                <tbody>';
     $sql = "SELECT * FROM tipoLesionSiniestro t order by tipo";
     $result = mysql_query($sql, $conn);
     while ($data = mysql_fetch_array($result)){
           $sql="SELECT count(*) as actual,
      (SELECT count(*)
       FROM siniestros where tipo_lesion = s.tipo_lesion and
            fecha_siniestro between DATE_ADD('2016-11-01', INTERVAL -1 YEAR) and DATE_ADD('2016-12-31', INTERVAL -1 YEAR) $str) as anterior,
       round(count(*)/12, 2) as media_mensual
FROM siniestros s
where fecha_siniestro between '$desde' and '$hasta' and not borrada and s.tipo_lesion = $data[0] and afecta_estadistica $str
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
$tabla.='</tbody></table>
                         </fieldset>';
     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">3. COSTOS ESTIMADOS</legend>
                         </fieldset>';
     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
		                 <legend class="ui-widget ui-widget-header ui-corner-all">4. CAUSAS FUENTES</legend>
                                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                                   <legend class="ui-widget ui-widget-header ui-corner-all">4.1 RESPONSABILIDAD</legend>
                                           <table class="table table-zebra">
                                                  <thead>
                                                  <tr>
                                                      <th>Responsabilidad</th>
                                                      <th>Cantidad</th>
                                                      <th>Conductores</th>
                                                  </tr>
                                                  </thead>
                                                  <tbody>';
                                           $sql="SELECT * FROM resp_estimada_siniestro order by responsabilidad";
                                           $result = mysql_query($sql, $conn);
                                           $stru="";
                                           if ($_POST[str]){
                                              $stru="and s.id_estructura = $_POST[str]";
                                           }
                                           while ($data = mysql_fetch_array($result)){

                                                 $sql = "SELECT upper(apellido)
                                                         FROM siniestros s
                                                         inner join empleados e on e.id_empleado = s.id_empleado
                                                         where fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica and resp_estimada = $data[0] $stru";
                                                 $res = mysql_query($sql, $conn) or die($sql);
                                                 $cant=0;
                                                 $cnd="";
                                                 while ($row = mysql_fetch_array($res)){
                                                       $cant++;
                                                       if ($data[0] != 2)
                                                          $cnd.="$row[0], ";
                                                 
                                                 }
                                                 $tabla.="<tr>
                                                              <td>$data[1]</td>
                                                              <td>$cant</td>
                                                              <td>$cnd</td>
                                                          </tr>";
                                           }
		                                   
     $tabla.='</tbody></table></fieldset>
                                 <fieldset class="ui-widget ui-widget-content ui-corner-all">
		                                   <legend class="ui-widget ui-widget-header ui-corner-all">4.2 CONDUCTA APROPIADA NO REALIZADA</legend>
		                                   <table class="table table-zebra">
                                                  <thead>
                                                  <tr>
                                                      <th>N'.htmlentities('°').'</th>
                                                      <th>Conducta</th>
                                                      <th>Cantidad</th>
                                                  </tr>
                                                  </thead>
                                                  <tbody>';
                                           $sql="SELECT * FROM normas_seg_vial ";
                                           $result = mysql_query($sql, $conn);
                                           while ($data = mysql_fetch_array($result)){
                                                 $sql = "SELECT count(*)
                                                         FROM siniestros s
                                                         where fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica and norma_no_respetada = $data[0] $str";
                                                 $res = mysql_query($sql, $conn);
                                                 if ($row = mysql_fetch_array($res)){

                                                 }
                                                 if ($row[0])
                                                 $tabla.="<tr>
                                                              <td>$data[0]</td>
                                                              <td>$data[1]</td>
                                                              <td align='right'>$row[0]</td>
                                                          </tr>";
                                           }
     $tabla.='</tbody></table></fieldset>';
                         
    $sql = "SELECT maniobra, count(*) as actual
            FROM siniestros s
            left join tipo_maniobra_siniestro tm on tm.id = s.tipo_maniobra
            where fecha_siniestro between '$desde' and '$hasta' and not borrada and afecta_estadistica $str
            group by tipo_maniobra";
            
    $result = mysql_query($sql, $conn);
    $tipo = "<table class='table table-zebra'>
                    <thead>
                           <tr>
                               <th>Situacion Siniestro</th>
                               <th>Cantidad</th>
                           </tr>
                    </thead>
                    <tbody>";
    while ($data = mysql_fetch_array($result)){
          $tipo.="<tr>
                      <td>$data[0]</td>
                      <td>$data[1]</td>
                  </tr>";
    }
    $tipo.="<tbody>
            </table>";


     $tabla.='<fieldset class="ui-widget ui-widget-content ui-corner-all">
                       <legend class="ui-widget ui-widget-header ui-corner-all">4.3 SITUACION SINIESTRO</legend>
                       '.$tipo.'
            </fieldset>
            </fieldset>';
    print $tabla;
  }
  
  function resumenPorEstado($conn, $desde, $hasta, $str){
           $stru='';
           if ($str)
              $stru=" and s.id_estructura = $str";
           $sql = "select estado, count(*) as cant, 1 as delperiodo
                  from (SELECT id, upper(estado) as estado FROM estadosiniestro e
                        union all
                        select 0 as id, 'NO DETALLA' as estado) e
                  left join  (select if (s.id_estado is null, 0, id_estado) as id_estado, fecha_siniestro
                              from siniestros s
                              where not borrada and afecta_estadistica $stru) s on s.id_estado = e.id
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
                              where not borrada and afecta_estadistica $stru) s on s.id_estado = e.id
                  where fecha_siniestro < '$desde'
                  group by id_estado";
      $result = mysql_query($sql, $conn);
      $resumen = array();
      while ($data = mysql_fetch_array($result)){
            $resumen[$data[estado]][$data[delperiodo]] = $data[cant];
      }
      
        return $resumen;
  }

  
?>

