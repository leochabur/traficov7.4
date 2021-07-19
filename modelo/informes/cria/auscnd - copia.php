<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'reskm'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $sql = "SELECT upper(nov_text), sum(if ((desde >= '$desde') and (hasta <= '$hasta')
                         ,DATEDIFF(hasta, desde)+1,
                         if ((desde < '$desde') and (hasta > '$hasta'),
                             DATEDIFF('$hasta', '$desde')+1,
                             if (desde < '$desde',
                                DATEDIFF(hasta, '$desde')+1,
                                DATEDIFF('$hasta', desde)+1
                             )
                         )
                     )) as dias, count(distinct(n.id_empleado)) as personal
FROM novedades n
inner join empleados e on e.id_empleado = n.id_empleado
inner join cod_novedades c on c.id = n.id_novedad
where ((desde between '$desde' and '$hasta') or (hasta between '$desde' and '$hasta') or ((desde < '$desde')and(hasta > '$hasta'))) and n.activa and n.id_estructura = $_POST[str]
group by c.id
order by nov_text";
//die($sql);
     $conn = conexcion();
     
     $sql_cant_emp = "SELECT count(*)
                  FROM empleados e
                  where activo and id_estructura = $_SESSION[structure] and id_cargo = 1 and id_empleador = 1";
     $result = mysql_query($sql_cant_emp, $conn);
     $cant_emp = 0;
     if ($row = mysql_fetch_array($result)){
        $cant_emp = $row[0];
     }

     $result = mysql_query($sql, $conn);
     
     $datos = array();
     $cant_novedades = 0;
     $cant_empleados = 0;
     while ($row = mysql_fetch_array($result)){
           $cant_novedades+=$row[1];
           $cant_empleados+=$row[2];
           $datos[$row[0]] = array(0 => $row[1], 1 => $row[2]);
     }
     
     
     
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" align="center" width="75%">
                     <thead>
            	            <tr class="ui-widget-header">
                                <th>Novedad</th>
                                <th>Cant. Dias</th>
                                <th>% Total Dias</th>
                                <th>Cant. Empleados</th>
                                <th>% Total Empleados</th>
                             </tr>
                     </thead>
                    <tbody>';

   //  $data = mysql_fetch_array($result);
   $i=0;
   $por=0;
     foreach ($datos as $clave => $valor){

                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>$clave</td>
                              <td align='right'>$valor[0]</td>
                              <td align='right'>".round(($valor[0]/$cant_novedades)*100,3)." %</td>
                              <td align='right'>$valor[1]</td>
                              <td align='right'>".round(($valor[1]/$cant_emp)*100,3)." %</td>

                          </tr>";
                 $por+=($valor[0]/$cant_novedades)*100;
                 $i++;

     }
     $tabla.='</tbody>
              <tr bgcolor="#606060"><td><b>TOTALES</b></td>
                  <td align="right"><b>'.$cant_novedades.'</b></td>
                  <td></td>
                  <td align="right"><b>'.$cant_empleados.'</b></td>
                  <td></td>
                  </tr>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">

                  </script>
';
    print $tabla;
  }
  
?>

