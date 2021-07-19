<?php

  session_start();
  date_default_timezone_set('America/Los_Angeles');
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  function datetomin($date){
           $hours = (integer)$date->format('%h');
           $min = (integer)$date->format('%i');
           return (($hours*60)+$min);
  }

  function datetomin2($date){
           $hours = $date->format("H");
           $min = $date->format("i");
           return (($hours*60)+$min);
  }
  
  function redondearMinutos($minutes){
           $hour = ($minutes - ($minutes % 60)) / 60;
           $mins = $minutes%60;
           if ($mins <= 15){
              return ($hour * 60);
           }
           elseif ($mins > 15 && $mins <= 45){

               //   die("son hs $hour");
                  return (($hour*60) + 30);
           }
           else{
                return (($hour * 60)+60);
           }
  }


function mintohour($min){
         $hours=floor($min/60);
         $min=$min%60;
         if($min < 10)
         return "$hours:0$min";
         else
         return "$hours:$min";
}
  if($accion == 'hscond'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
   //  if ($_POST['emple']){
           $table=allDriver($desde, $hasta, $_POST['emple']);
  //   }
  //   else{
  //        $table=allDriver($desde, $hasta);
  //   }
    print $table;
  }
  
  function getOrderPrevius($cond, $conn, $fecha){
      $sql = "select concat(fservicio, ' ', hcitacion) as hcita,
                     if (hfinservicio < hcitacion, concat(DATE_ADD(fservicio, INTERVAL 1 DAY),' ',hfinservicio), concat(fservicio, ' ', hfinservicio)) as hfina
                from ordenes
                where fservicio < '$fecha' and $cond in (id_chofer_1, id_chofer_2)
                order by fservicio DESC, hcitacion DESC
                LIMIT 1";
      if ($row = mysql_fetcha_array(mysql_query($sql, $conn)))
        return $row;
  }
  
  function completeArrayHs($horas, $minutos){ ///recibe un array con las horas y los mintuos para ver donde los aplica
      if ($minutos > 480){
          $horas['hnormales'] = 480;
          $minutos-= 480;
          if ($minutos > 120){
              $horas['h50'] = 120;
              $minutos-=120;
              if ($minutos > 0){
                  $horas['h100'] = $minutos;
              }
          }
          else{
              $horas['h50'] = $minutos;
          }
      }
      else{
          $horas['hnormales'] = $minutos;
      }
      return $horas;
  }
  
  function allDriver($desde, $hasta, $empl, $minCorteTurno = 480){
           $condemp1="";
           $condemp2="";
           if ($empl){
              $condemp1="and (id_chofer_1 = $empl)";
              $condemp2="and (id_chofer_2 = $empl)";
           }
$sql = "select o.*, concat(apellido,', ', nombre) as emple, legajo, concat(fservicio_inicio,' ',hcitacion) as dtdesde, 
              concat(fservicio_fin,' ',hfinservicio) as dthasta, 
                DAYOFWEEK(fservicio_inicio) as dia_inicio, DAYOFWEEK(fservicio_fin) as dia_fin, e.id_ciudad as ciudad_residencia
            from(
            SELECT id_chofer_1 as cond, hcitacion, hsalida, hllegada, hfinservicio,
                   fservicio as fservicio_inicio,
                   if (hfinservicio < hcitacion, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio) as fservicio_fin,
                   TIME_TO_SEC(hcitacion) as cita, TIME_TO_SEC(hfinservicio) as fin,
                   TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs,
                   id_ciudad_origen as origen, id_ciudad_destino as destino, id_chofer_1 as id_fercho
FROM ordenes o
where (id_estructura = 1) and (id_chofer_1 is not null) $condemp1 and (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida)
union all
SELECT id_chofer_2 as cond, hcitacion, hsalida, hllegada, hfinservicio,
           fservicio as fservicio_inicio,
           if (hfinservicio < hcitacion, DATE_ADD(fservicio, INTERVAL 1 DAY), fservicio) as fservicio_fin,
       TIME_TO_SEC(hcitacion) as cita, TIME_TO_SEC(hfinservicio) as fin,
       TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs,
       id_ciudad_origen as origen, id_ciudad_destino as destino, id_chofer_2 as id_fercho
FROM ordenes o
where (id_estructura = 1) and (id_chofer_2 is not null) $condemp2 and(fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida)
) o
inner join empleados e on e.id_empleado = o.cond
where (e.id_empleador = 1)
order by apellido, nombre, fservicio_inicio, hcitacion, hsalida";
//die($sql);

$tabla='<table id="example" name="example">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Conductor</th>
                        <th>Jornadas Procesadas</th>
                        <th>Hs. Normales</th>
                        <th>Hs. al 50</th>
                        <th>Hs. al 100</th>
                    </tr>
                    </thead>
                    </tbody>';
$conn = conexcion();
     $ajuste_corte = 0; //para ajustar la sensibilidad del corte del turno
     $result = mysql_query($sql, $conn) or die("ok");
     $data = mysql_fetch_array($result);
     $minutes="";
     $total_min_normales = 0;
     $total_min_50 = 0;
     $total_min_100 = 0;   
     $i=0; $j=0;
     $incidencias="";
     $ult_dia_semana_inicio = 0;
     $ult_dia_semana_fin = 0;
     while ($data){
           $ultOrden = "";
           $cond = $data['cond'];
           $emple = $data['emple'];
           $legajo = $data['legajo'];
           $resumenHs = array('hnormales' => 0, 'h50' => 0, 'h100' => 0);
           while (($data) &&($cond == $data['cond'])){
                 $desde = new DateTime($data['dtdesde']);
                 $hasta = new DateTime($data['dthasta']);
                
                 if (!$ultOrden){ ///primer orden procesada para el conductor
                     ///debe levantar la ulrima ordenes anterior para ver si la orden actual viene de un turno del dia anterior
                     $back = getOrderBack($cond, $conn, $desde->format('Y-m-d'));
                     if ($back){
                         $ultHora = new DateTime($back['hfina']);
                         $minutos = redondearMinutos(datetomin($ultHora->diff($desde)));
                         while (($data) && ($cond == $data['cond']) && ($minutos < $minCorteTurno)){   /////quiere decir que la orden actual viene de un turno del dia anterior
                             $ultHora = new DateTime($data['hfina']);
                             $data = mysql_fetch_array($result);
                             $desde = new DateTime($data['dtdesde']);
                             $hasta = new DateTime($data['dthasta']);
                             $minutos = redondearMinutos(datetomin($ultHora->diff($desde)));
                         }
                     }
                     ////es la primera orden pr procesar, ya descarto las que vienen del dia anterior
                     $minutos_orden = redondearMinutos(datetomin($desde->diff($hasta)));
                     $resumenHs = completeArrayHs($resumenHs, $minutos_orden);
                 }
                 else{
                     
                 }


                 if ($cambio_conductor){
                        $total_min_normales+=redondearMinutos($minutos_acumulados_dia);;
                        $total_min_50+=redondearMinutos($minutos_al_50);
                        $total_min_100+=redondearMinutos($minutos_al_100);
                 }
                 else{
                 $sql="SELECT id_novedad
                          FROM novedades
                          where '$data[fservicio_inicio]' between desde and hasta and id_novedad in (16, 18) and id_empleado = $data[id_fercho] and id_estructura = $_SESSION[structure]";
                 $resul_nov = mysql_query($sql, $conn); //consulta para saber si el dia siguiente a una orden de feriado o franco es tambien un franco o feriado
                 if (!mysql_fetch_array($resul_nov))

                 {
                 /////
                 $legajo=$data['legajo'];
                 $emple=$data['emple'];
                 $desde = new DateTime($data['dtdesde']);
                 $hasta = new DateTime($data['dthasta']);
                 
                 if ($ultima_vuelta){
                    if($desde < $ultima_vuelta){
                              $desde = new DateTime($ultima_vuelta->format('Y-m-d H:i:s'));
                    }
                 }
                 if ($hasta < $desde){
                       $hasta = new DateTime($desde->format('Y-m-d H:i:s'));
                       $incidencias="";
                 }

                 $acata=0;
                 $min_del_servicio=0;
                 $ctos.="<br>$minutos_al_100";
                 
                 $especial=true;
                 if ($data['dia_inicio'] == 7){
                    if ($ult_dia_semana_fin == 6){
                       $especial=false;
                    }
                 }
                 elseif(($data['dia_inicio'] == 7) && ($ult_dia_semana_fin == 6)){
                    $especial=false;
                 }
                 elseif(($data['dia_inicio'] == 2)&&($ult_dia_semana_fin == 1)){
                    $especial = false;
                 }
                 if (!$especial){
                        $reiniciar = true;
                        $total_min_normales+=redondearMinutos($minutos_acumulados_dia);
                        $minutos_acumulados_dia=0;
                        $total_min_50+=redondearMinutos($minutos_al_50);
                        $minutos_al_50=0;
                        $total_min_100+=redondearMinutos($minutos_al_100);
                        $minutos_al_100=0;
                        $corte=0;
                        $ultima_vuelta=0;
                 }
                 else{
                 
                 if (!($reiniciar)){
                     if ($desde >=$corte100){

                        $reiniciar = true;
                        $total_min_normales+=redondearMinutos($minutos_acumulados_dia);
                        $minutos_acumulados_dia=0;
                        $total_min_50+=redondearMinutos($minutos_al_50);
                        $minutos_al_50=0;
                        $total_min_100+=redondearMinutos($minutos_al_100);
                        $minutos_al_100=0;
                        $corte=0;
                        $ultima_vuelta=0;
                     }
                     else{
                          if ($hasta >= $corte100){
                             if(datetomin($ultima_vuelta->diff($desde)) >= 720){

                             }
                             else{
                             }
                             $reiniciar = true;
                             $total_min_normales+=redondearMinutos($minutos_acumulados_dia);
                             $minutos_acumulados_dia=0;
                             $total_min_50+=redondearMinutos($minutos_al_50);
                             $minutos_al_50=0;
                             $total_min_100+=redondearMinutos($minutos_al_100);
                             $minutos_al_100=0;
                             $corte=0;
                             $ultima_vuelta=0;
                          }
                          else{
                               if(datetomin($ultima_vuelta->diff($desde)) >= 720){
                                                                          $reiniciar = true;
                                                                          $total_min_normales+=redondearMinutos($minutos_acumulados_dia);
                                                                          $minutos_acumulados_dia=0;
                                                                          $total_min_50+=redondearMinutos($minutos_al_50);
                                                                          $minutos_al_50=0;
                                                                          $total_min_100+=redondearMinutos($minutos_al_100);
                                                                          $minutos_al_100=0;
                                                                          $corte=0;
                                                                          $ultima_vuelta=0;
                               }
                          }
                     }
                 }
                 }
                 $ctos.=" Acumulados $total_min_100";
                 if ($reiniciar){
                    $corte50 = new DateTime($desde->format('Y-m-d H:i:s'));
                    $corte100 = new DateTime($desde->format('Y-m-d H:i:s'));
                    $corte50->add(new DateInterval('P0Y0DT12H0M'));//representa el primer corte de hs, es decir las primeras 12 hs donde se pagan horas al 50
                    $corte100->add(new DateInterval('P0Y1D'));//idem anterior pero las primeras 24 hs
                    $reiniciar=false;
                    $corte=0;
                    $cant_jornadas++;
                 }
                 $minutos_corte=0;
                 if ($corte){ //si ya realizo un corte directamente calcula la diferencia de tiempo entre la ultima vueta y el inicio de la actual
                    $minutos_corte = datetomin($ultima_vuelta->diff($desde));
                 }
                 else{
                    if ($ultima_vuelta){ //calcula si hay corte o no
                       $minutos_corte = datetomin($ultima_vuelta->diff($desde));
                       $cortar_turno = true;
                       if (($minutos_corte + $ajuste_corte) >= 120){
                       
                            if ($ultimo_destino){    //ya proceso al menos un servicio
                               if (($ultimo_destino != 1) && ($ultimo_destino != 3)){   //esta fuera de cabecera, no hay que cortar el turno
                                  if ($data['ciudad_residencia'] == $ultimo_destino)
                                     $cortar_turno = true;
                                  else
                                      $cortar_turno = false;
                               }
                            }
                            if ($cortar_turno){
                               $corte++;
                               $minutos_corte=0;
                            }
                       }
                    }
                 }
                 if ($hasta <= $corte50){  //la hora de finalizacion del servicio esta dentro de las primeras 12 hs de iniciado el turno
                    $min_del_servicio = $minutos_corte;
                    $min_del_servicio+= datetomin($desde->diff($hasta)); //calcula la duracion de la orden actual

                    if (($minutos_acumulados_dia+$min_del_servicio) > 480){ //si la suma de los acumulado mas lo de la orden actual supera las 8 hs, debe compuatr hs al 50
                       $minutos_al_50+= $min_del_servicio - (480 - $minutos_acumulados_dia);
                       $minutos_acumulados_dia = 480;
                    }
                    else{
                         $minutos_acumulados_dia+=$min_del_servicio;
                    }
                 }
                 elseif (($desde <= $corte50) && ($hasta > $corte50)){
                      $auxii= $minutos_al_50;
                       $min_hasta_hs_al_50 = $minutos_corte;
                       $min_hasta_hs_al_50+=  datetomin($desde->diff($corte50)); //calcula la diferencia entre el inicio de la orden y el primer corte a las 12 hs de iniciado el turno
                       if (($minutos_acumulados_dia + $min_hasta_hs_al_50) > 480){ //si la suma de los acumulado mas lo de la orden actual supera las 8 hs, debe compuatr hs al 50
                          $minutos_al_50+= ($minutos_acumulados_dia+$min_hasta_hs_al_50)-480; //todos los minutos que exeden las 8 hs son tomados al 50
                          $minutos_acumulados_dia = 480;
                       }
                       else{
                            $minutos_acumulados_dia+= $min_hasta_hs_al_50;
                       }
                       $minutos_al_100+= datetomin($corte50->diff($hasta));  //diferencia entre el corte al 50 y el fin de orden para calcular horas al 100
                       $i++;
                 }
                 elseif ($desde > $corte50){
                        $resto_min=0;
                        $minutos_srv = datetomin($desde->diff($hasta));

                        if ($ultima_vuelta){
                           if ($corte50 > $ultima_vuelta && $corte50 < $desde){
                              if (!$corte){

                              $min_hasta = datetomin($ultima_vuelta->diff($corte50));
                              if (($minutos_acumulados_dia + $min_hasta) > 480){ //si la suma de los acumulado mas lo de la orden actual supera las 8 hs, debe compuatr hs al 50
                                 $minutos_al_50+= ($minutos_acumulados_dia+$min_hasta)-480; //todos los minutos que exeden las 8 hs son tomados al 50
                                 $minutos_acumulados_dia = 480;
                              }
                              else{
                                   $minutos_acumulados_dia+=$min_hasta;

                                   if (($minutos_corte - $min_hasta) > 0){
                                      $minutos_al_100+= ($minutos_corte - $min_hasta)+$minutos_srv;
                                   }
                              }
                              }
                           }
                           else{
                                $minutos_al_100+= ($minutos_srv+$minutos_corte);

                           }
                        }
                     //   die("corto aca $corte (corte) $minutos_srv  $minutos_al_50 ".$ultima_vuelta->format('Y-m-d H:i:s')." ".$corte50->format('Y-m-d H:i:s'));
                 }
                 $ultima_vuelta = new DateTime($hasta->format('Y-m-d H:i:s'));
                 $ultimo_destino = $data['destino'];

                 $ult_dia_semana_inicio = $data['dia_inicio'];
                 $ult_dia_semana_fin = $data['dia_fin'];
                 $data = mysql_fetch_array($result);
                 }
                 }
           }
           $total_min_normales+=redondearMinutos($minutos_acumulados_dia);
           $minutos_acumulados_dia=0;
           $total_min_50+=redondearMinutos($minutos_al_50);
           $minutos_al_50=0;
           $total_min_100+=redondearMinutos($minutos_al_100);
           $minutos_al_100=0;
           $tabla.="<tr>
                        <td>$legajo</td>
                        <td>$emple</td>
                        <td align='right' width='15%'>$cant_jornadas</td>
                        <td align='right' width='15%'>".mintohour($total_min_normales)."</td>
                        <td align='right' width='15%'>".mintohour($total_min_50)."</td>
                        <td align='right' width='15%'>".mintohour($total_min_100)."</td>
                    </tr>";
    //       die($ctos." Acumulados $total_min_100");
       //    die("minutos acumulados dia: ".mintohour($total_min_normales)."    minutos al 50: ".mintohour($total_min_50)."            minutos al 100: ".mintohour($total_min_100));
     }
     $tabla.='</tbody>
              </table><style type="text/css">
                         #example { font-size: 75%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script type="text/javascript">
                          $("#example").dataTable({
					                                    "sScrollY": "400px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                     "sInfoFiltered": "(Filtro sobre _MAX_ registros)"}
				                                       });
                          $("#example tbody tr").dblclick(function(){
                                                                     var cond = $(this).attr("id");
                                                                     $("#emples option[value="+cond+"]").attr("selected", "selected");
                                                                     $("#emples").selectmenu({width: 350});
                                                                     $("#dats").html("<div align=\'center\'><img  alt=\'cargando\' src=\'../../ajax-loader.gif\'/></div>");
                                                                      $.post("/modelo/informes/cria/hsxcond.php", {accion:"hscond", desde: $("#desde").val(), hasta: $("#hasta").val(), emple: cond}, function(data){
                                                                                                                                                                                                                               $("#dats").html(data);
                                                                                                                                                                                                                               });
                                                                     });
                  </script>  ';
     print $tabla;
  }
  
  function uniqueDriver($desde, $hasta, $emple){
$sql = "select o.*, concat(apellido,', ', nombre) as emple, legajo, date_format(fservicio, '%d/%m/%Y') as pretty
            from(
            SELECT id_chofer_1 as cond, hcitacion, hsalida, hllegada, hfinservicio, fservicio, TIME_TO_SEC(hcitacion) as cita, TIME_TO_SEC(hfinservicio) as fin,
                   TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs
FROM ordenes o
where (id_estructura = 1) and (fservicio between '$desde' and '$hasta') and (id_chofer_1 is not null)
union all
SELECT id_chofer_2 as cond, hcitacion, hsalida, hllegada, hfinservicio, fservicio, TIME_TO_SEC(hcitacion) as cita, TIME_TO_SEC(hfinservicio) as fin,
       TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs
FROM ordenes o
where (id_estructura = 1) and (fservicio between '$desde' and '$hasta') and (id_chofer_2 is not null)
) o
inner join empleados e on e.id_empleado = o.cond
where (e.id_empleador = 1) and (e.id_empleado = $emple)
order by fservicio, hcitacion";

     $conn = conexcion();

     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="75%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Fecha</th>
                        <th>Hs. Efectivas</th>
                        <th>Hs Ociosas</th>
                        <th>Hs. a Liquidar</th>
                    </tr>
                    </thead>
                    <tbody>';

     $result = mysql_query($sql, $conn);
     $data = mysql_fetch_array($result);
     while ($data){
           $tot = 0;
           $ociosas = 0;
                 $fecha = $data['fservicio'];
                 $pretty = $data['pretty'];
                 $ulhora="";
                 while (($data)&& ($fecha == $data['fservicio'])){
                       if ($ulhora != ""){
                          if ((($data['cita'] - $ulhora) < 14400) && (($data['cita'] - $ulhora) > 0)){
                             $tot+=($data['cita'] - $ulhora);
                             $ociosas+=($data['cita'] - $ulhora);
                          }
                       }
                       $ulhora=$data['fin'];
                       $tot+=$data['hs'];
                       $data = mysql_fetch_array($result);
               }
           $liquidar = makeTimeFromSeconds($tot+$ociosas);
           $tot= makeTimeFromSeconds($tot);
           $ociosas=makeTimeFromSeconds($ociosas);
           $tabla.="<tr>
                            <td align='left'>$pretty</td>
                            <td align='right'>$tot</td>
                            <td align='right'>$ociosas</td>
                            <td align='right'>$liquidar </td>
                            </tr>";
     }
     $tabla.='</tbody>
              </table><style type="text/css">
                         #example { font-size: 75%; }
                         #example tbody tr.even:hover, #example tbody tr.even td.highlighted {background-color: #ECFFB3;}
                         #example tbody tr.odd:hover, #example tbody tr.odd td.highlighted {background-color: #E6FF99;}
                         #example tr.even:hover {background-color: #ECFFB3;}
                         #example tr.even:hover td.sorting_1 {background-color: #DDFF75;}
                         #example tr.even:hover td.sorting_2 {background-color: #E7FF9E;}
                         #example tr.even:hover td.sorting_3 {background-color: #E2FF89;}
                         #example tr.odd:hover {background-color: #E6FF99;}
                         #example tr.odd:hover td.sorting_1 {background-color: #D6FF5C;}
                         #example tr.odd:hover td.sorting_2 {background-color: #E0FF84;}
                         #example tr.odd:hover td.sorting_3 {background-color: #DBFF70;}
                  </style>
                  <script type="text/javascript">
                          $("#example").dataTable({
					                                    "sScrollY": "400px",
					                                    "bPaginate": false,
					                                    "bScrollCollapse": true,
					                                    "bJQueryUI": true,
					                                    "oLanguage": {
                                                                     "sLengthMenu": "Display _MENU_ records per page",
                                                                     "sZeroRecords": "Sin Registros para mostrar",
                                                                     "sInfo": "",
                                                                     "sInfoEmpty": "Showing 0 to 0 of 0 records",
                                                                     "sInfoFiltered": "(Filtro sobre _MAX_ registros)"}
				                                       });
                  </script>  ';
     return $tabla;
  }
  
  /*                 if ($ultima_fecha != $data['fservicio_inicio']){
                    $sql="SELECT id_novedad
                          FROM novedades
                          where '$data[fservicio_inicio]' between desde and hasta and id_novedad in (16, 18) and id_empleado = $data[id_fercho]";
                 //   die($sql);
                    $res_nov = mysql_query($sql, $conn);
                    if ($data_novedad = mysql_fetch_array($res_nov)){
                       if ($data_novedad['id_novedad'] == 16){  ///encuentra novedad de franco trabajado
                          $inicio_franco_trab =    //toma el iniciio de la joranda
                          $fecha_franco_trab = $data['fservicio_inicio'];
                          $es_dia_domingo = $data['dia_inicio'];
                          while (($data) &&($cond == $data['cond']) && ($data['fservicio_inicio'] == $fecha_franco_trab)){
                                $fin_fr_tr= $data['dthasta'];
                                $data = mysql_fetch_array($result);
                          }
                          $cant_jornadas++;
                          $fin_franco_tr = new DateTime($fin_fr_tr);
                          $min_gen_franco_trabajado = redondearMinutos(datetomin($inicio_franco_trab->diff($fin_franco_tr)));
                          if ($min_gen_franco_trabajado < 480){
                             $total_min_100+= 480;
                          }
                          else{
                              $total_min_100+= $min_gen_franco_trabajado;
                          }
                          if ($cond != $data['cond']){ //cambio el conductor que se venia procesando
                             $cambio_conductor = true;
                          }
                       }
                       if ($data_novedad['id_novedad'] == 18){  ///encuentra novedad de feriado trabajado

                    //      $fecha_franco_trab = $data['fservicio_inicio'];
                          $es_dia_domingo = $data['dia_inicio'];
                          while (($data) &&($cond == $data['cond']) && ($data['fservicio_inicio'] == $fecha_franco_trab)){
                                $inicio_feriado_trab = new DateTime($data['dtdesde']);
                                $fin_feriado_tr = new DateTime($data['dthasta']);
                                $min_gen_feriado_trabajado = redondearMinutos(datetomin($inicio_feriado_trab->diff($fin_feriado_tr)));
                                $total_min_100+= $min_gen_feriado_trabajado;
                                $data = mysql_fetch_array($result);
                          }
                          $cant_jornadas++;
                          if ($cond != $data['cond']){ //cambio el conductor que se venia procesando
                             $cambio_conductor = true;
                          }
                       }
                    }
                 }*/
?>

