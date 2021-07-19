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
  
  function ajustarHora($hora){
           $mm= $hora->format('i');
           if (($mm > 15) && ($mm <= 45)){
              $nueva = new DateTime($hora->format("Y-m-d H:30:00"));
           }
           elseif($mm <= 15){
              $nueva = new DateTime($hora->format("Y-m-d H:00:00"));
           }
           elseif($mm > 45){
              $hh= $hora->format('H')+1;
              $nueva = new DateTime($hora->format("Y-m-d $hh:00:00"));
                      // die("a varrrrr ".$inicio->format('H:i:'));
           }
           return $nueva;
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
     $hasta = $desde;//dateToMysql($_POST['hasta'], '/');
   //  if ($_POST['emple']){
           $table=allDriver($desde, $hasta, $_POST['emple']);
  //   }
  //   else{
  //        $table=allDriver($desde, $hasta);
  //   }
    print $table;
  }
  
  function allDriver($desde, $hasta, $empl){
           $condemp1="";
           $condemp2="";
           if ($empl){
              $condemp1="and (id_chofer_1 = $empl)";
              $condemp2="and (id_chofer_2 = $empl)";
           }
$sql = "select o.*, concat(apellido,', ', nombre) as emple, legajo, concat(fservicio_inicio,' ',hcitacion) as dtdesde, concat(fservicio_fin,' ',hfinservicio) as dthasta, DAYOFWEEK(fservicio_inicio) as dia_inicio, DAYOFWEEK(fservicio_fin) as dia_fin, e.id_ciudad as ciudad_residencia
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
//die ($sql);

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
     $resultado = array();
     $x=1;
     while ($data){
           $cond = $data[cond];
           $ultorden="";
           $hs=0;
           $hs50=0;
           $hs100=0;
           $corte=0;
           $draw=array();
           while (($data) &&($cond == $data['cond'])){
                 $inicio = new DateTime($data['dtdesde']);
                 $fin =new DateTime($data['dthasta']);
                 $mins = ($fin->format('U') - $inicio->format('U'))/60; ///calcula el tiempo de la orden actual
                 $minCorte = 0;
                 if (!$ultorden){  ////primera orden procesada
                      $hInicioTurno = clone $inicio;
                      $corte12 = clone $hInicioTurno;
                      $corte12->add(new DateInterval("PT12H"));
                      if ($mins <= 480){
                         $hs+=$mins;
                         $horai = ajustarHora($inicio);
                         $horaf = ajustarHora($fin);
                         $rango = ($horaf->format('U') - $horai->format('U'))/60;
                         for ($i=0; $i <= ($rango/30); $i++){
                             $draw[$horai->format('H:i')] = 1;
                             $horai->add(new DateInterval("PT30M"));
                         }
                      }
                      elseif($mins <= 720){
                         $hs=480;
                         $hs50+=($mins-480);
                      }
                      else{
                         $hs=480;
                         $hs50=720;
                         $hs100+=($mins-720);
                      }
                 }
                 else{ ////ha procesado al menos una orden
                      $finUltOrden = new DateTime($ultorden['dthasta']);   ////hora de fin de la ultima orden procesada

                      //////fragmento de codigo para corregir superpocicion horaria
                      if ($finUltOrden > $inicio){
                         $inicio = clone $finUltOrden;
                         if ($inicio > $fin){
                            $fin = clone $inicio;
                         }
                      }
                      //////////////////fin correccion solapamiento
                      
                      $minCorte = ($inicio->format('U') - $finUltOrden->format('U'))/60;
                      if ($minCorte > 120){
                         if (!$corte){
                              $minCorte = 0;
                              $corte++;
                         }
                      }
                      if ($fin <= $corte12){ ///la orden finaliza dentro de las 12 primeras horas
                         if ($hs+$mins+$minCorte > 480){ /// trabajo mas de 8 hs
                         
                          //  $iniAux = clone $inicio;
                        //    $iniAux->add(new DateInterval("PT".$mins50."M"));




                            $mins50 = ($hs+$mins+$minCorte)-480;
                          //  die("minutos $mins50");
                            $inicio->add(new DateInterval("PT".$mins50."M"));
                            $horai = ajustarHora($inicio);
                            $horaf = ajustarHora($fin);
                            $rango = ($horaf->format('U') - $horai->format('U'))/60;
                            for ($i=0; $i <= ($rango/30); $i++){
                                  $draw[$horai->format('H:i')] = 2;
                                  $horai->add(new DateInterval("PT30M"));
                            }

                            $hs50+= ($hs+$mins+$minCorte)-480;
                            $hs = 480;
                         }
                         else{
                              $hs+=$mins+$minCorte;
                              $horai = ajustarHora($inicio);
                              $horaf = ajustarHora($fin);
                              $rango = ($horaf->format('U') - $horai->format('U'))/60;
                              for ($i=0; $i <= ($rango/30); $i++){
                                  $draw[$horai->format('H:i')] = 1;
                                  $horai->add(new DateInterval("PT30M"));
                              }


                              if (!$corte){
                                 $horai = ajustarHora($finUltOrden);
                                 $horaf = ajustarHora($inicio);
                                 $rango = ($horaf->format('U') - $horai->format('U'))/60;
                                 for ($i=0; $i <= ($rango/30); $i++){
                                     $draw[$horai->format('H:i')] = 1;
                                     $horai->add(new DateInterval("PT30M"));
                                 }
                              }
                         }
                      }
                      elseif(($inicio <= $corte12)&&($fin > $corte12)){
                         $minsAlCorte = (($corte12->format('U') - $inicio->format('U'))/60);

                         if (($hs+$minsAlCorte+$minCorte) > 480){ /// trabajo mas de 8 hs
                            $alCorte50 = (($hs+$minsAlCorte+$minCorte)-480);
                            
                            $horaf = clone $corte12;
                            $horaf->sub(new DateInterval("PT".$alCorte50."M"));
                            $horai = clone $inicio;
                            $horai = ajustarHora($horai);
                            $horaf = ajustarHora($horaf);
                            $rango = ($horaf->format('U') - $horai->format('U'))/60;
                            for ($i=0; $i <= ($rango/30); $i++){
                                     $draw[$horai->format('H:i')] = 1;
                                     $horai->add(new DateInterval("PT30M"));
                            }
                            
                            /////////////imprime horas al 50////////////
                            $horai = clone $corte12;
                            $horai->sub(new DateInterval("PT".$alCorte50."M"));
                            $horaf = clone $corte12;
                            $horai = ajustarHora($horai);
                            $horaf = ajustarHora($horaf);
                            $rango = ($horaf->format('U') - $horai->format('U'))/60;
                            for ($i=0; $i <= ($rango/30); $i++){
                                     $draw[$horai->format('H:i')] = 2;
                                     $horai->add(new DateInterval("PT30M"));
                            }
                            ///////////////////////////
                            
                            


                           // die ("al socor  $alCorte50   ");
                            $hs50+=(($hs+$minsAlCorte+$minCorte)-480);
                            $hs = 480;
                            
                            
                            
                            
                            
                            
                         //   die("minutos al corte $minsAlCorte sisisi");
                         }
                         else{
                              $hs+=($minsAlCorte+$minCorte);
                         }
                         
                         $horai = ajustarHora($corte12);
                         $horaf = ajustarHora($fin);
                         $rango = ($horaf->format('U') - $horai->format('U'))/60;
                         for ($i=0; $i <= ($rango/30); $i++){
                                     $draw[$horai->format('H:i')] = 3;
                                     $horai->add(new DateInterval("PT30M"));
                         }
                         
                         $minsPostCorte = ($fin->format('U') - $corte12->format('U'))/60;
                         $hs100+=$minsPostCorte;
                      }
                      else{
                           $hs100+=$mins+$minCorte;
                           $horai = ajustarHora($inicio);
                           $horaf = ajustarHora($fin);
                           $rango = ($horaf->format('U') - $horai->format('U'))/60;
                           for ($i=0; $i <= ($rango/30); $i++){
                                     $draw[$horai->format('H:i')] = 3;
                                     $horai->add(new DateInterval("PT30M"));
                           }
                      }
                 }
                 $ultorden = $data;
                 $data = mysql_fetch_array($result);
           }
           $resultado[$ultorden[emple]] = $draw;
        //   print ("$ultorden[emple]   Hs Normales: ".mintohour($hs)." - Hs 50: ".mintohour($hs50)." - Hs. 100: ".mintohour($hs100)."<br>");
           $ultorden="";
     }
    // die (print_r($resultado));


     $tabla = "<style>.dge {
  font-family: serif;
  font-size: 75%;
}</style><table border='1' class='dge'>
                      <tr>
                          <td>Conductor</td>";

     
     $inicio = new DateTime("$desde 00:00:00");
     $escala = array();
     $j=0;
     for ($i = 0; $i <= 48; $i++){
         $tabla.="<td>".$inicio->format('H:i')."</td>";
         $escala[$j++] = $inicio->format('H:i');
         $inicio->add(new DateInterval("PT30M"));
     }
     $tabla.="</tr>";
     
     ksort($resultado);
     
     foreach ($resultado as $clave => $valor) {
        $tabla.="<tr> <td>$clave</td>";
             foreach ($escala as $k => $v) {
                     if (array_key_exists($v, $valor)){
                        if ($valor[$v] == 1)
                           $color = "#008000";
                        elseif($valor[$v] == 3){
                           $color = "#FF0000";
                        }
                        elseif($valor[$v] == 2){
                           $color = "#FFFF00";
                        }
                        $tabla.="<td bgcolor='$color'></td>";
                     }
                     else{
                          $tabla.="<td></td>";
                     }
             }
        $tabla.="</tr>";
            // print_r($valor);
        //     print "<br><br>";
          //   $tabla.="<tr><td>$clave</td></tr>";
     }
    // die (print_r($escala));
   //  die($inicio->format('H:i'));



     print $tabla;
  }
  
?>

