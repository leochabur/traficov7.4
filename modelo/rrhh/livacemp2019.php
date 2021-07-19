<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
 // include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if($accion == 'prel'){
      $emple = $_POST['emple'];
      $cond_emple='';
      if ($emple != 0)
         $cond_emple ="and id_empleado = $emple";
      $puesto = $_POST['puesto'];
      if($puesto != 0)
         $cond_puesto = "and id_cargo = $puesto";
      $conn = conexcion();
      $sql = "SELECT legajo, upper(concat(apellido,', ',nombre)) as apenom, date_format(inicio_relacion_laboral, '%d/%m/%Y'),
                     datediff('$_POST[anio]-12-31', inicio_relacion_laboral) as dias,
                                          (

                     (SELECT sum(cant_dias) FROM vacacionespersonal v where v.id_empleado = e.id_empleado)

                     -
                     (SELECT sum((datediff(hasta, desde)+1)) as dias FROM novedades n where (n.id_empleado = e.id_empleado) and (activa) and (id_novedad = 19) and (desde > '2014-06-30'))
                     )as tot_vac, id_empleado
              FROM empleados e
                     WHERE (activo) and (not borrado) and (id_empleador = 1) and ((SELECT cant_dias FROM vacacionespersonal v where v.id_empleado = e.id_empleado and anio = $_POST[anio]) is null) $cond_emple $cond_puesto
                     ORDER BY apenom";

      $result = mysql_query($sql, $conn);
      $tabla='<div<p align="right"><input type="checkbox" id="all-empty" name="allempty" onClick="checkTodos(this)">Todos/Ninguno</p></div>
              <table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Fecha Ingreso</th>
                        <th>Dias Acumulados</th>
                        <th>Vacaciones a&ntilde;o '.$_POST[anio].'</th>
                        <th>Si/No</th>
                    </tr>
                    </thead>
                    <tbody>';
      $i=0;
      while ($row = mysql_fetch_array($result)){
                $dias = $row[dias];
                if ($dias >= 7300){
                   $dias = 35;
                }
                elseif($dias >= 3650){
                             $dias = 28;
                }
                elseif($dias >= 1825){
                             $dias = 21;
                }
                elseif($dias >= 180){
                             $dias = 14;
                }
                else{
                     $dias = round($dias/20);
                }
               $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
               $tabla.="<tr bgcolor='$color'>
                            <td align='left'>$row[0]</td>
                            <td align='left'>$row[1]</td>
                            <td align='right'>$row[2]</td>
                            <td align='right'>$row[tot_vac]</td>
                            <td align='right'><input type='text' size='7' value='$dias' style='text-align:right;' id='dias-$row[id_empleado]'></td>
                            <td align='center'><input type='checkbox' id='$row[id_empleado]'></td>
                            </tr>";
               $i++;
      }
      $tabla.='</tbody>
              </table>
              <input type="button" value="Generar Vacaciones" id="generar">
                  <style>
                         #example { font-size: 85%; }
                         #example tbody tr:hover {
                                                 background-color: #FF8080;
                                                 }
                  </style>
                  <script>
                      function checkTodos (obj) {
                                                $("#example input:checkbox").attr("checked", obj.checked);
                      }
                          $("#example input[type=text]").focus(function(){
                                                                          this.select();

                                                                          });
                          $("#generar").button().click(function(){
                                                         var vacaciones = new Array();
                                                         $("#example input:checked").each(function(){
                                                                                                     if($(this).is(":checked")){
                                                                                                                                var dias = $("#dias-"+$(this).attr("id")).val();
                                                                                                                                var tupla = $(this).attr("id")+"-"+dias;
                                                                                                                                vacaciones.push(tupla);

                                                                                                     }
                                                                                                     });
                                                         $.post("/modelo/rrhh/livacemp.php", {vacas:vacaciones.join(), anio: '.$_POST[anio].', accion: "liquidar"}, function(data){
                                                                                                                                                                                    $.post("/modelo/rrhh/livacemp.php", {accion:"prel", puesto: $("#puesto").val(), anio: $("#anios").val(), emple:$("#emples").val()}, function(data){
                                                                                                                                                                                                                                                                                                                                       $("#dats").html(data);
                                                                                                                                                                                                                                                                                                                                       });
                                                         
                                                         
                                                         
                                                                                                                                                                                   });
                          });
                  </script>';
    print $tabla;
  }
  elseif($accion == 'liquidar'){
         $conn = conexcion();
         $vacaciones = explode(',', $_POST['vacas']);
         $resu='';
        // die($_POST[anio]);
         foreach ($vacaciones as $valor) {
                 $valor = explode('-', $valor);
                 //$resu.="- id: ".$valor[0]."  dias:".$valor[1];
                         
                 $sql = "insert into vacacionespersonal (id_empleado, anio, fecha_liquidacion, cant_dias, id_user, detalle)
                                                  values(".$valor[0].", $_POST[anio], now(), ".$valor[1].", $_SESSION[userid], 'Vacaciones anio $_POST[anio]')";
                 mysql_query($sql, $conn);
         }
                 mysql_close($conn);
                 print $resu;
  }

?>

