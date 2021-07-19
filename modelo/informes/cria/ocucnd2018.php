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
     $hasta = $desde;//'2018-04-12';//$desde;
     loadSub($desde, $hasta, $_POST['emple']);//dateToMysql($_POST['hasta'], '/');
   //  if ($_POST['emple']){
          // $table=allDriver($desde, $hasta, $_POST['emple']);
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

$tabla='<table id="example" name="example" class="table table-zebra">
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
}</style><table border='1' class='dge table table-zebra'>
                      <tr>
                          <td>Conductor</td>";

     
     $inicio = new DateTime("$desde 00:00:00");
     $escala = array();
     $j=0;
     for ($i = 0; $i < 48; $i++){
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
     $tabla.="</tbody></table>";


     print $tabla;
  }
  
  
  function loadSub($desde, $hasta, $emple){
     if ($emple){
     $condemp1 = "and id_chofer_1 = $emple";
     $condemp2 = "and id_chofer_2 = $emple";
     }
     $conn = conexcion();
     $ajuste_corte = 0; //para ajustar la sensibilidad del corte del turno
     $sql = getSQL($desde, $hasta, $condemp1, $condemp2);
    // die($sql);
     $result = mysql_query($sql, $conn) or die("ok");
     $data = mysql_fetch_array($result);
     $novedades = arrayNovedades($desde,$conn);
     $datis = "";
     $paint = array();
     while ($data){
           $cond = $data[cond];
           $emple = $data[emple];
           $resu = array();

           while (($data) &&($cond == $data['cond'])){
                 $emple = $data[emple];
                 $resu[] = $data;
                 $data = mysql_fetch_array($result);
           }

           $resultado =  calcularHsChofer($resu, $novedades);
           $tabla = array(0=>$emple, 1=>$resultado[3]);
           $paint[$cond] = $tabla;
           $datis.="$emple => Hs normales: ".mintohour($resultado[0])." , Hs 50: ".mintohour($resultado[1]).", Hs 100 ".mintohour($resultado[2])."<br>";
     }
     $inicio = new DateTime("$desde 00:00:00");
     $escala = array();
     $j=0;
     $tabla="<table class='table table-zebra lalala'>
                    <thead>
                    <tr>
                        <th></th>";
     for ($i = 0; $i < 48; $i++){
         $tabla.="<th>".$inicio->format('H:i')."</th>";
         $escala[$j++] = $inicio->format('H:i');
         $inicio->add(new DateInterval("PT30M"));
     }
     $tabla.="</tr>
                   </thead>
                   <tbody>";
     
     foreach ($paint as $clave => $valor) {
             $tabla.="<tr> <td>$valor[0]</td>";
             foreach ($escala as $k => $v) {
                     if (array_key_exists($v, $valor[1])){
                        if ($valor[1][$v] == 1)
                           $color = "#008000";
                        elseif($valor[1][$v] == 3){
                           $color = "#FF0000";
                        }
                        elseif($valor[1][$v] == 2){
                           $color = "#FFFF00";
                        }
                        $tabla.="<td bgcolor='$color'></td>";
                     }
                     else{
                          $tabla.="<td></td>";
                     }
                     
             }
             $tabla.="<tr>";
     }
     $tabla.="</tbody></table>
                              <script>
                                      $('.lalala').stickyTableHeaders();
                              </script>";
   //  die(print_r($paint));
     print $tabla;
  }
  
  function calcularHsChofer($result, $novedades, $ajuste=15){       /// $novedades[id_empleado] = id_novedad
           $hs100 = 0;
           $hsNormales = 0;
           $hs50 = 0;
           $francoTrabajado = false; /// para saber si al finalizar el procedimiento debe calcular las horas como franco trabajado
           $log="cantidad de servicios ".count($result)."</br>";
           $i=1;
           $pendiente = true;
           $resultado = array();
           $draw = array();
           $paint = array();
           $ultorden="";
           $corte = 0;
           $pendienteDraw;
           foreach ($result as $data){
                   $inicio = new DateTime($data['dtdesde']);///inicio de la vuelta
                   $fin = new DateTime($data['dthasta']);    /// fin de la vuelta
                   if (array_key_exists($data['cond'], $novedades) && ($novedades[$data['cond']] == 18)){      ///feriado trabajado calcula las horas efectivas al 100%
                      $hs100.= ($fin->format('U') - $inicio->format('U'))/60;
                      $ultorden = $data;
                   }
                   elseif($novedades[$data['cond']] == 16){ ///franco trabajado desde el inicio al fin todo al 100
                      if (!$ultorden){
                         $comienzaFranco = clone $inicio;
                      }
                      $finalizaFranco = clone $fin;   ///hay que analizar si comienza un dia y termina al otro dia, seria franco trabajado???
                      $francoTrabajado = true;
                      $ultorden = $data;
                      $hs100+= ($fin->format('U') - $inicio->format('U'))/60;
                   }
                   else{  ///jornada normal
                          $mins = ($fin->format('U') - $inicio->format('U'))/60; ///calcula el tiempo de la orden actual
                          if (!$ultorden){   ////primera orden procesada
                             $resultado = procesarOrdenInicio($inicio, $fin, $paint);
                             $hsNormales = $resultado[0];
                             $hs50 = $resultado[1];
                             $hs100 = $resultado[2];
                             $corte12 = $resultado[3];
                             $ultorden = $data;
                             $pendiente = true;
                             $pendienteDraw = $resultado[4];
                          }
                          else{
                                ////proceso al menos una orden
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
                                if ($minCorte > 480){ ///esta procesando una orden correspondiente aldia anterior, se debe descartar
                                   if ($pendiente){
                                      $paint = array();
                                      $resultado = procesarOrdenInicio($inicio, $fin, $paint);
                                      $hsNormales = $resultado[0];
                                      $hs50 = $resultado[1];
                                      $hs100 = $resultado[2];
                                      $corte12 = $resultado[3];
                                      $paint = $resultado[4];
                                      $ultorden = $data;
                                   }
                                }
                                else{
                                        if ($pendienteDraw){
                                           $paint = $pendienteDraw;
                                           $pendienteDraw = "";
                                        }
                                        if (($minCorte +$ajuste) >= 120){
                                           if (!$corte){
                                              $minCorte = 0;
                                              $corte++;
                                           }
                                        }
                                        if ($fin <= $corte12){ ///la orden finaliza dentro de las 12 primeras horas
                                           if ($hsNormales+$mins+$minCorte > 480){ /// trabajo mas de 8 hs
                                              $mins50 = ($hsNormales+$mins+$minCorte)-480;
                                              $hs50+= ($hsNormales+$mins+$minCorte)-480;
                                              $hsNormales = 480;
                                           }
                                           else{
                                                if ($corte){
                                                   $paint = pintarFila($inicio, $fin, 1, $paint);
                                                }
                                                else{
                                                     $paint = pintarFila($finUltOrden, $fin, 1, $paint);

                                                }
                                                   // pintarFila($inicio, $fin, $color, $draw)
                                                $hsNormales+=$mins+$minCorte;
                                           }
                                        }
                                        elseif(($inicio <= $corte12)&&($fin > $corte12)){ ////la orden comienza antes de las 12 hs y termina despues de las 12hs de iniciado el turno
                                                $minsAlCorte = (($corte12->format('U') - $inicio->format('U'))/60);
                                                if (($hsNormales+$minsAlCorte+$minCorte) > 480){ /// trabajo mas de 8 hs
                                                   ///solo paa saber de que color debe pintar la celda  ///////////
                                                   $faltante8hs = (480 - $hsNormales);
                                                   $saldoExedente = ($minsAlCorte+$minCorte) - $faltante8hs;
                                                   if ($corte == 0){
                                                      $finPaint8hs = clone $finUltOrden;
                                                      $finPaint8hs->add(new DateInterval("PT".$faltante8hs."M"));
                                                      $paint = pintarFila($finUltOrden, $finPaint8hs, 1, $paint);
                                                      $paint = pintarFila($finPaint8hs, $corte12, 2, $paint);
                                                      //$paint = pintarFila($corte12, $fin, 3, $paint);
                                                   }
                                                   else{

                                                      $finPaint8hs = clone $inicio;
                                                      if ($faltante8hs > 0){
                                                         try{
                                                            /* if ($faltante8hs > 60){
                                                                $mins = $faltante8hs % 60;
                                                                $hour = floor($faltante8hs/60);
                                                                $finPaint8hs->add(new DateInterval("PT.".$hour."H".$mins."M"));
                                                             }
                                                             else{ */
                                                                 $finPaint8hs->add(new DateInterval("PT".$faltante8hs."M"));
                                                             //}
                                                         } catch (Exception $e) {die("conductor $data[cond] horario $faltante8hs");}
                                                      }
                                                      
                                                      $paint = pintarFila($inicio, $finPaint8hs, 1, $paint);
                                                      $paint = pintarFila($finPaint8hs, $corte12, 2, $paint);
                                                      //$paint = pintarFila($corte12, $fin, 3, $paint);
                                                   
                                                   }
                                                   ////////////////////////////////////////////////////////////////
                                                   $alCorte50 = (($hsNormales+$minsAlCorte+$minCorte)-480);
                                                   $hs50+=(($hsNormales+$minsAlCorte+$minCorte)-480);
                                                   $hsNormales = 480;
                                                }
                                                else{
                                                     if ($corte == 0){
                                                        $paint = pintarFila($finUltOrden, $fin, 1, $paint);
                                                     }
                                                     else{
                                                          $paint = pintarFila($inicio, $fin, 1, $paint);
                                                     }
                                                     $hsNormales+=($minsAlCorte+$minCorte);
                                                }
                                                $paint = pintarFila($corte12, $fin, 3, $paint);
                                                $minsPostCorte = ($fin->format('U') - $corte12->format('U'))/60;
                                                $hs100+=$minsPostCorte;
                                        }
                                        else{ ////inicia despus de las 12 hs
                                             $hs100+=$mins+$minCorte;
                                             $paint = pintarFila($inicio, $fin, 3, $paint);
                                        }
                                        $ultorden = $data;
                              }
                          }
                   }
                   $i++;
           }
         //  die(print_r($paint));
         //  die($log);
           return array(0=>$hsNormales, 1=>$hs50, 2=>$hs100, 3=>$paint);
  }
  
  function procesarOrdenInicio($inicio, $fin, $draw){
           //$draw = array();
           $mins = ($fin->format('U') - $inicio->format('U'))/60;
           $corte12 = clone $inicio;
           $corte12->add(new DateInterval("PT12H")); ///hora hasta la cual debe calcular horas al 50
           if ($mins <= 480){ //trabajo 8 hs
              $hsNormales=$mins;
              $draw = pintarFila($inicio, $fin, 1, $draw);
           }
           elseif($mins <= 720){ //trabajo menos de 12 hs
                        $hfin8 = clone $inicio;
                        $hfin8->add(new DateInterval('PT8H'));
                        $draw = pintarFila($inicio, $hfin8, 1, $draw);
                        $hsNormales=480;
                        $hs50=($mins-480);
                        $draw = pintarFila($hfin8, $fin, 2, $draw);
           }
           else{
                        $hfin8 = clone $inicio;
                        $hfin8->add(new DateInterval('PT8H'));
                        $draw = pintarFila($inicio, $hfin8, 1, $draw);
                        $hfin12 = clone $hfin8;
                        $hfin12->add(new DateInterval('PT4H'));
                        $draw = pintarFila($hfin8, $hfin12, 2, $draw);
                        $draw = pintarFila($hfin12, $fin, 3, $draw);
                        $hsNormales=480;
                        $hs50=240;
                        $hs100=($mins-720);
           }
           return array(0=>$hsNormales, 1=>$hs20, 2=>$hs100, 3=>$corte12, 4=> $draw);
  }
  
  function pintarFila($inicio, $fin, $color, $draw){
           $horai = ajustarHora($inicio);
           $horaf = ajustarHora($fin);
           $rango = ($horaf->format('U') - $horai->format('U'))/60;
           $last='';
           for ($i=0; $i <= ($rango/30); $i++){
                  if ($last){
                     if (!($last->format('H') > $horai->format('H'))){
                        $draw[$horai->format('H:i')] = $color;
                        $last = $horai;
                     }
                  }
                  else{
                       $last = $horai;
                       $draw[$horai->format('H:i')] = $color;
                  }

                  $horai->add(new DateInterval("PT30M"));
           }
           return $draw;
  }
  
  function arrayNovedades($fecha, $conn){
           $sql = "select id_empleado, id_novedad
                   from novedades
                   where '$fecha' between desde and hasta and id_novedad in (16, 17) and activa";
           $result = mysql_query($sql, $conn);
           $data = array();
           while ($row = mysql_fetch_array($result)){
                 $data[$row[0]] = $row[1];
           }
           return $data;
  
  }
  
  function getSQL($desde, $hasta, $condemp1, $condemp2){
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
           return $sql;
  }
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
  
?>

