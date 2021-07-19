<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }

  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');

 // include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'prel'){
      $emple = $_POST['emple'];
      $cond_emple='';
      if ($emple != 0)
         $cond_emple ="and e.id_empleado = $emple";
      $puesto = $_POST['puesto'];
      $cond_puesto='';
      if($puesto != 0)
         $cond_puesto = "and id_cargo = $puesto";
      $detalle = $_POST['det'];
      if ($detalle != '')
         $cond_detalle = "and detalle = '$detalle'";
      $conn = conexcion();
      $sql = "SELECT legajo, upper(concat(apellido,', ',nombre)) as nombre, date_format(inicio_relacion_laboral, '%d/%m/%Y') as ingreso, detalle, cant_dias, v.id, c.descripcion
              FROM vacacionespersonal v
              inner join empleados e on e.id_empleado = v.id_empleado
              inner join cargo c on c.id = e.id_cargo
              where e.activo $cond_emple $cond_puesto $cond_detalle
              order by apellido";
      $result = mysql_query($sql, $conn);
      $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Fecha Ingreso</th>
                        <th>Puesto</th>
                        <th>Detalle</th>
                        <th>Cant. Dias</th>
                        <th>Modificar</th>
                    </tr>
                    </thead>
                    <tbody>';
      $i=0;
      while ($row = mysql_fetch_array($result)){
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='right'>$row[0]</td>
                            <td align='left'>$row[1]</td>
                            <td align='center'>$row[2]</td>
                            <td align='left'>$row[6]</td>
                            <td align='left'>$row[3]</td>
                            <td align='right'><input type='text' size='7' value='$row[4]' style='text-align:right;' id='dias-$row[5]'></td>
                            <td align='center'><input type='button' value='Modificar' id='$row[5]'></td>
                            </tr>";
               $i++;
      }
      $tabla.="</tbody>
              </table>
                  <style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {
                                                 background-color: #FF8080;
                                                 }
                  </style>
                  <script>

                          $('#example input:button').button().click(function(){
                                      var id = $(this).attr('id');
                                      var dias = $('#dias-'+id).val();
                                      $.post('/modelo/rrhh/modvcemp.php', {accion:'modif', id_liq: id, days: dias}, function(data){});
                                      $(this).hide();
                          });
                  </script>";
    print $tabla;
  }
  elseif($accion == 'modif'){
                 update("vacacionespersonal", "cant_dias", "$_POST[days]", "id = $_POST[id_liq]");
  }
  elseif($accion == 'salin'){
                 $conn = conexcion();
                 $sql = "INSERT INTO vacacionespersonal (id_empleado, anio, fecha_liquidacion, cant_dias, id_user, detalle)
                         VALUES ($_POST[emple],0,now(), $_POST[days], $_SESSION[userid], 'Saldo Inicial') ON DUPLICATE KEY UPDATE cant_dias = $_POST[days]";
                 mysql_query($sql, $conn);
                 
                 if (!mysql_errno($conn))
                    $ok = json_encode(1);
                 else
                     $ok = json_encode(0);
                 mysql_close($conn);
                 print $ok;
  }

?>

