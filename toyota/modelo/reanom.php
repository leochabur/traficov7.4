<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];


  if($accion == 'list'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

     $sql = "SELECT date_format(fecha, '%d/%m/%Y') as fecha,
                    interno,
                    if (causa is null, rubro, concat(rubro, ' (',origen,')')) as rubro,
                    detalle_anomalia,
                    date_format(a.fecha_alta, '%d/%m/%Y - %H:%i') as generada,
                    upper(apenom) as creada_por,
                    observacion_taller,
                    a.id,
                    reparada,
                    date_format(fecha_reparacion, '%d/%m/%Y') as frepara,
                    a.fecha_alta
             FROM anomalias a
             inner join rubros_anomalias r on r.id = a.id_rubroanomalia
             inner join unidades u on u.id = a.id_unidad
             inner join usuarios us on us.id = a.id_usuario_alta
             inner join origen_anomalias oa on oa.id = a.causa
             where  activa and fecha between '$desde' and '$hasta' and us.id AND id_cliente = 10
             order by a.fecha_alta";
  //   die($sql);
     $conn = conexcion(true);
     
     $result = mysqli_query($conn, $sql);

     
     $tabla='<table id="example" name="example" class="table table-zebra" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Fecha</th>
                        <th>Interno</th>
                        <th>Rubro</th>
                        <th>Detalle</th>
                        <th>Fecha creacion</th>
                        <th>Generada por</th>
                        <th>Fecha reparacion</th>
                        <th>Observacion taller</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysqli_fetch_array($result);
     while ($data){
               $tabla.="<tr id='$data[7]'>
                            <td align='center'>$data[0]</td>
                            <td align='right'>$data[1]</td>
                            <td align='left'>$data[2]</td>
                            <td align='left'>$data[3]</td>
                            <td align='center'>$data[4]</td>
                            <td align='left'>$data[5]</td>
                            <td align='center'>$data[frepara]</td>
                            <td align='left'>$data[6]</td>
                            </tr>";
               $data = mysqli_fetch_array($result);
     }
     mysqli_free_result($result);
     mysqli_close($conn);
     $tabla.='</tbody>
              </table>';
    print $tabla;
  }
  elseif($accion == 'modanom'){
                 $repa = 0;
                 $fecha = "NULL";
                 $hora = "NULL";
                 if (isset($_POST['rpda'])){
                    $repa = 1;
                    if ($_POST['frepa']){
                       $fecha = "'".dateToMysql($_POST['frepa'], '/')."'";
                    }
                    if ($_POST['hrepa']){
                       $hora = "'".$_POST['hrepa']."'";
                    }
                 }
                 try{
                    update("anomalias", "reparada, observacion_taller, fecha_reparacion, hora_reparacion, id_usuario_reparacion, orden_trabajo", "$repa, '$_POST[otaller]', $fecha, $hora, $_SESSION[userid], '$_POST[orepa]'", "(id = $_POST[anomalia])");
                    print "1";
                 }
                 catch (Exception $e) {
                       print "0";
                 }
  }
  
?>

