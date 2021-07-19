<?php
  session_start();
  set_time_limit(0);  
  error_reporting(0);  
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'reskm'){
     $xcond=1;
     if ($xcond){
        $order = "apellido, n.id_empleado, nov_text";
        $group = "n.id_empleado, c.id";
     }
     else{
          $order = "nov_text, apellido";
          $group = "c.id, n.id_empleado";
     }
     $cn="";
     if ($_POST[inc] == 'cn'){
        $cn = "and id_cargo = 1";
     }
     
     $desde = dateTimeToMonthYearMysql($_POST['desde'], '/').'-01';
     

     $days = daysOfMonth($_POST['hasta'], '/');
     $hasta = dateTimeToMonthYearMysql($_POST['hasta'], '/')."-$days";
     
     $meses = monthBetweenDate($desde, $hasta);
     $meses++;
     $meses = $meses*6;

     $sql = "SELECT upper(g.nombre) as grupo, g.id as idgpo, upper(nov_text) as novedad, c.id as codnov, concat(apellido,', ', e.nombre) as conductor, n.id_empleado as emple,
if ((desde >= '$desde') and (hasta <= '$hasta')
                         ,DATEDIFF(hasta, desde)+1,
                         if ((desde < '$desde') and (hasta > '$hasta'),
                             DATEDIFF('$hasta', '$desde')+1,
                             if (desde < '$desde',
                                DATEDIFF(hasta, '$desde')+1,
                                DATEDIFF('$hasta', desde)+1
                             )
                         )
                     ) as dias
FROM novedades n
inner join empleados e on e.id_empleado = n.id_empleado
inner join cod_novedades c on c.id = n.id_novedad
inner join anomxgrupoinforme axg on axg.id_cod_anomalia = c.id
inner join grupoanomaliasinfausentismo g on g.id = axg.id_grupo_informe
where ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta') or ((desde < '$desde')and(hasta > '$hasta'))) and n.activa and n.id_estructura = $_POST[str] $cn
order by g.nombre, nov_text, apellido, e.nombre";
//die($sql);

     $conn = conexcion();
     $nc = "select * from empleados where activo and id_empleador = 1 and not borrado and activo and id_estructura = 1 $cn";
     $result = mysql_query($nc, $conn);
     $count = mysql_num_rows($result);
     $count = ($count * $meses);
    // die($count." asda" );


     $result = mysql_query($sql, $conn) or die('erorrr');
     
     $datos = array();

     $cant_empleados = 0;
     
     $row = mysql_fetch_array($result);
     $noves = array();
     $grupos = array();
     $emples = array();
     while ($row){
           $gpo = $row[idgpo];
           $cant_dias_gpo = 0;
           $cant_novedades_gpo = 0;
           $cant_emples_gpo = 0;
           while (($row)&&($gpo == $row[idgpo])){
                 $nove = $row[codnov];
                 $cant_dias_nove = 0;
                 $cant_novedades_nove = 0;
                 $cant_emples_nove = 0; ///acumula la cantidad de empleados para una novedad determinada
                 $emples[$gpo][$nove] = array();
                 while (($row)&&($gpo == $row[idgpo]) && ($nove == $row[codnov])){
                       $cant_dias_nove+=$row[dias];
                       $cant_novedades_nove++;
                       if (!in_array($row[emple],$emples[$gpo][$nove])){
                          $emples[$gpo][$nove][] = $row[emple];
                          $cant_emples_nove++;
                       }
                       $row = mysql_fetch_array($result);
                 }
                 $noves[$nove] = array(0=>$cant_dias_nove, 1=>$cant_novedades_nove, 2=>$cant_emples_nove);
                 $cant_dias_gpo+= $cant_dias_nove;
                 $cant_emples_gpo+=$cant_emples_nove;
                 $cant_novedades_gpo+= $cant_novedades_nove;
           }
           $grupos[$gpo] = array(0 => $cant_dias_gpo, 1 => $cant_novedades_gpo, 2=> $cant_emples_gpo);
     }
     
     
     
     $tabla='<table id="example" name="example" class="table table-zebra" align="center" width="75%">
                     <thead>
            	            <tr class="ui-widget-header">
                                <th>Grupo de Novedades</th>
                                <th>Codigo Novedades</th>
                                <th>Apellido, Nombre</th>
                                <th>Cant. Empleados</th>
                                <th>Cant. Novedades</th>
                                <th>Cant. Dias</th>
                                <th>Incidencia (%)</th>
                             </tr>
                     </thead>
                    <tbody>';

   //  $data = mysql_fetch_array($result);
   $i=0;
   $por=0;
   $result = mysql_query($sql, $conn);
   $row = mysql_fetch_array($result);


   if ($xcond){ ///agruoalas novedades x conductor
      while ($row){
            $gpo = $row[idgpo];
            $tabla.="<tr data-tt-id='$row[idgpo]'>
                         <td><b>$row[grupo]</b></td>
                         <td></td>
                         <td></td>
                         <td align='right'><b>".$grupos[$gpo][2]."</b></td>
                         <td align='right'><b>".$grupos[$gpo][1]."</b></td>
                         <td align='right'><b>".$grupos[$gpo][0]."</b></td>
                         <td align='right'>".number_format((($grupos[$gpo][2]/$count)*100),4)." %</td>
                    </tr>";
            while (($row) &&($gpo == $row[idgpo])){
                  $nove = $row[codnov];
                  $tabla.="<tr data-tt-id='2.$row[codnov]' data-tt-parent-id='$row[idgpo]'>
                               <td></td>
                               <td>$row[novedad]</td>
                               <td></td>
                               <td align='right'>".$noves[$nove][2]."</td>
                               <td align='right'>".$noves[$nove][1]."</td>
                               <td align='right'>".$noves[$nove][0]."</td>
                               <td align='right'>".number_format((($noves[$nove][2]/$count)*100),4)." %</td>
                           </tr>";
                  while (($row) &&($gpo == $row[idgpo]) && ($nove == $row[codnov])){
                        $tabla.="<tr data-tt-id='3.$row[emple]' data-tt-parent-id='2.$row[codnov]'>
                                     <td></td>
                                     <td></td>
                                     <td>$row[conductor]</td>
                                     <td></td>
                                     <td align='right'>$row[cant_noves]</td>
                                     <td align='right'>$row[dias]</td>
                                     <td align='right'></td>
                                 </tr>";
                        $row = mysql_fetch_array($result);
                  }
            }
      }
   }
   
   $tabla.="</tbody></table>
       <script>
      $('#example').treetable({ expandable: true });
       $('#example tbody').on('mousedown', 'tr', function() {
        $('.selected').not(this).removeClass('selected');
        $(this).toggleClass('selected');
      });
      </script>";
    print $tabla;
  }
  
?>

