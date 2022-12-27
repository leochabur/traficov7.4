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

  if($accion == 'reskm')
  {
     if ((!$_POST['desde']) || (!$_POST['hasta']))
     {
        print "<b>Los campos Desde y Hasta deben estar completos";
        exit();
     }
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

     $sector = '';
     if ($_POST['sector'])
     {
         $sector = "join cargo c on c.id = e.id_cargo and c.id_estructura = e.id_estructura_cargo and c.id_sector = $_POST[sector] ";
     }

     $interno = '';

     $conn = conexcion(true);

     /*$sqlAccessos = "SELECT legajo, nrodoc, upper(concat(apellido,', ',nombre)) as empleado, a.stamp, if (sentido, 'INGRESO', 'EGRESO') as sentido
                     FROM accesosregistro a
                     join empleados e on e.id_empleado = a.id_empleado
                     $sector
                     WHERE a.stamp BETWEEN (UNIX_TIMESTAMP('$desde 00:00:00') * 1000) AND (UNIX_TIMESTAMP('$hasta 23:59:59') * 1000)
                     order by stamp DESC";*/

     $sqlAccessos = "SELECT legajo, nrodoc, upper(concat(apellido,', ',nombre)) as empleado, a.stamp, if (sentido, 'INGRESO', 'EGRESO') as sentido, e.id_empleado as id_emp
                     FROM accesosregistro a
                     join empleados e on e.id_empleado = a.id_empleado
                     $sector
                     WHERE a.stamp BETWEEN (UNIX_TIMESTAMP('$desde 00:00:00') * 1000) AND (UNIX_TIMESTAMP('$hasta 23:59:59') * 1000)
                     order by empleado, legajo, date(FROM_UNIXTIME(stamp/1000, '%Y-%m-%d')), stamp ASC";

    //  die($sqlAccessos);
      $accesos = mysqli_query($conn, $sqlAccessos) or die ($sqlAccessos);

      $tbody = "";

      $row = mysqli_fetch_array($accesos);

      while ($row)
      {
         $cond = $row['id_emp'];

         $ingreso = $egreso = $lastsent = '';

         while (($row) && ($row['id_emp'] == $cond))
         {


            if ($row['sentido'] == 'INGRESO')
            {
               //die('empleado '.$row['empleado']);
               if ($lastsent == 'INGRESO') //quiere decir que hay dos registros seguidos de ingreso
               {
                  
                  $fecha = new DateTime();
                  $fecha->setTimestamp($last['stamp']/1000);            
                  $tbody.="<tr class='align-middle'>
                                 <td>$last[legajo]</td>
                                 <td>".str_replace('.','',$last['nrodoc'])."</td>
                                 <td>$last[empleado]</td>
                                 <td>".$fecha->format('d/m/Y - H:i:s')."</td>
                                 <td></td>
                           </tr>";
                  $ingreso = '';
               }
               else
               {
                  
                  $fecha = new DateTime();
                  $fecha->setTimestamp($row['stamp']/1000); 
                  $ingreso = $fecha->format('d/m/Y - H:i:s');                  
               }
               $lastsent = $row['sentido'];
            }
            elseif ($row['sentido'] == 'EGRESO')
            {
               $fecha = new DateTime();
               $fecha->setTimestamp($row['stamp']/1000);            
               $tbody.="<tr class='align-middle'>
                              <td>$row[legajo]</td>
                              <td>".str_replace('.','',$row['nrodoc'])."</td>
                              <td>$row[empleado]</td>
                              <td>$ingreso</td>
                              <td>".$fecha->format('d/m/Y - H:i:s')."</td>                              
                        </tr>";
               $ingreso = $lastsent = '';
            }

            
            $last = $row;

            $row = mysqli_fetch_array($accesos);
         }
         if ($ingreso)
         {
            $fecha = new DateTime();
            $fecha->setTimestamp($last['stamp']/1000);
            $tbody.="<tr class='align-middle'>
                           <td>$last[legajo]</td>
                           <td>".str_replace('.','',$last['nrodoc'])."</td>
                           <td>$last[empleado]</td>                           
                           <td>".$fecha->format('d/m/Y - H:i:s')."</td>
                           <td></td>
                     </tr>";
         }
         $lastsent = null;
      }
      mysqli_close($conn);

      $tabla='<table id="example" name="example" class="table table-zebra" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">

                              <th>Legajo</th>
                              <th>Documento</th>
                              <th>Apellido, Nombre</th>
                              <th>Ingreso</th>
                              <th>Egreso</th>
                    </tr>
                    </thead>
                    <tbody>'.$tbody.'
                    </tbody>
                    </table>
                     <style>
                           #example { font-size: 85%; }
                           #example tbody tr:hover {
                                                      background-color: #FF8080;
                                                   }
                     </style>';
      print $tabla;
  }
  
?>

