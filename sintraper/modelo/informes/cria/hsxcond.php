<?

  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  function datetomin($date){
           $hours = $date->format("%H");
           $min = $date->format("%i");
           return (($hours*60)+$min);
  }
    function datetomin2($date){
           $hours = $date->format("H");
           $min = $date->format("i");
           return (($hours*60)+$min);
  }
  function makeTimeFromSeconds( $total_seconds ){
$horas              = floor ( $total_seconds / 3600 );
$minutes            = ( ( $total_seconds / 60 ) % 60 );
$seconds            = ( $total_seconds % 60 );

$time['horas']      = str_pad( $horas, 2, "0", STR_PAD_LEFT );
$time['minutes']    = str_pad( $minutes, 2, "0", STR_PAD_LEFT );
$time['seconds']    = str_pad( $seconds, 2, "0", STR_PAD_LEFT );

$time               = implode( ':', $time );

return $time;
}

function mintohour($min){
         $hours=floor($min/60);
         $min=$min%60;
         if($min < 10)
         return "$hours:0$min:00";
         else
         return "$hours:$min:00";
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
  
  function allDriver($desde, $hasta, $empl){
           $condemp1="";
           $condemp2="";
           if ($empl){
              $condemp1="and (id_chofer_1 = $empl)";
              $condemp2="and (id_chofer_2 = $empl)";
           }
$sql = "select o.*, concat(apellido,', ', nombre) as emple, legajo, concat(fservicio,' ',hcitacion) as dtdesde, concat(fservicio,' ',hfinservicio) as dthasta
            from(
            SELECT id_chofer_1 as cond, hcitacion, hsalida, hllegada, hfinservicio, fservicio, TIME_TO_SEC(hcitacion) as cita, TIME_TO_SEC(hfinservicio) as fin,
                   TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs
FROM ordenes o
where (id_estructura = 1) and (id_chofer_1 is not null) $condemp1 and (fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida)
union all
SELECT id_chofer_2 as cond, hcitacion, hsalida, hllegada, hfinservicio, fservicio, TIME_TO_SEC(hcitacion) as cita, TIME_TO_SEC(hfinservicio) as fin,
       TIME_TO_SEC(if(hfinservicio > hcitacion, timediff(hfinservicio, hcitacion), ADDTIME(timediff('23:59:00', hcitacion),timediff(hfinservicio, '00:00:00')))) as hs
FROM ordenes o
where (id_estructura = 1) and (id_chofer_2 is not null) $condemp2 and(fservicio between '$desde' and '$hasta') and (not borrada) and (not suspendida)
) o
inner join empleados e on e.id_empleado = o.cond
where (e.id_empleador = 1)
order by apellido, nombre, fservicio, hcitacion";

$conn = conexcion();
$tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="75%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Conductor</th>
                        <th>Hs. Diagramadas</th>
                        <th>Hs. Trabajadas</th>
                        <th>Hs. Normales</th>
                        <th>Hs. 50 %</th>
                        <th>Hs. 100 %</th>
                    </tr>
                    </thead>
                    <tbody>';
                    
$formato = 'Y-m-d H:i:s';

     $result = mysql_query($sql, $conn) or die("ok");
     $data = mysql_fetch_array($result);
     $minutes="";
     while ($data){
           $cond = $data['cond'];
           $emple = $data['emple'];
           $legajo = $data['legajo'];
           $ult='';
           $mt=0; $mtacum=0;
           $md=0; $mdacum=0;
           $h50=0;$h50acum=0;
           $h100=0;$h100acum=0;
           $mins="";
           $mn=0; $mnacum=0;//minutos normales trabajador
           $corte=0;//para indicar si ya realizo el corte correspondiente al cambio de turno
           while (($data) &&($cond == $data['cond'])){
                 $desde = new DateTime($data['dtdesde']);
                 $hasta = new DateTime($data['dthasta']);
                 if($ult != ''){
                              if ($ult > $desde){
                                 $desde = $ult;
                                 $mindif = 0;
                              }
                              else{
                                   $date = $hasta->diff($desde);
                                   $hours = $date->format("%H");
                                   $min = $date->format("%i");

                                   $mindif = (($hours*60)+$min);
                                   //die("ok   ".$mindif);
                             }


                             if(($mindif < 120) && ($mindif > 0)){ //no hay corte espera menor a dos horas
                                         $md+=$mindif;
                             }
                             else{ //existe un corte
                                   $corte++;
                             }
                 }
                 if($ult){
                         // die("ejem ".$mindif);
                 }
                 if (($corte > 1) || ($mindif > 359)){
                             $ult='';
                             $mtacum+=$mt;
                             $mdacum+=$md;
                             $h50acum+=$h50;
                             $h100acum+=$h100;
                             $mnacum+=$mn;
                             $mt=0;
                             $md=0;
                             $h50=0;
                             $h100=0;
                             $corte=0;
                             $mn=0;
                             $corte=0;
                 }
                 else
                     $ult=$hasta;
                 $mt+=datetomin($desde->diff($hasta));
                 $md+=datetomin($desde->diff($hasta));

                 $mn=$mt;
                 if ($mt > 480){
                    $mn = 480;
                    if ($mt < 720){
                       $h50 = $mt - 480;
                    }
                    else{
                         $h50=240;
                         $h100=$md-720;
                    }
                 }

                 $data = mysql_fetch_array($result);
           }
           $tabla.="<tr id='$cond'>
                            <td align='left'>$legajo</td>
                            <td align='left'>".htmlentities($emple)."</td>
                            <td align='right'>".mintohour($mdacum)."</td>
                            <td align='right'>".mintohour($mtacum)."</td>
                            <td align='right'>".mintohour($mnacum)."</td>
                            <td align='right'>".mintohour($h50acum)."</td>
                            <td align='right'>".mintohour($h100acum)."</td>
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
     return $tabla;
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
  
?>

