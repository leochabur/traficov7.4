<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
    include ('../controlador/bdadmin.php');
  include ('../controlador/ejecutar_sql.php');
  include_once ('../modelo/utils/dateutils.php');
  
  $accion = $_POST['accion'];

  if($accion == 'sveanom'){ //codigo para guardar una anomalia
             $fecha = dateToMysql($_POST['fecha'], '/');
             try{
                 $ok = insert('anomalias', 'id, id_empleado, id_unidad, id_rubroanomalia, fecha, detalle_anomalia, fecha_alta',
                              "$_SESSION[id_chofer], $_POST[unidad], $_POST[tipo], '$fecha', '$_POST[desc]', now()"); //agrega un conductor pendiente de procesamiento por parte del sector de RRHH
                 print  json_encode($ok);
             } catch (Exception $e) {
                     print json_encode(0);
             }
  }
  elseif($accion == 'load'){

                 $option = "<option value='1'>Todas las novedades</option>";
                 $show="";
                 if (isset($_POST['s']))
                 {
                    if ($_POST['s'] == '2'){
                      
                      $show = "AND a.id_empleado = $_SESSION[id_chofer]";
                      $option = "<option value='2'>Solo generadas por mi</option>";
                    }
                 }
                $tabla.="<fieldset class='ui-widget ui-widget-content ui-corner-all'>
                            <legend class='ui-widget ui-widget-header ui-corner-all'>Filtrar anomalias</legend>
                              <table width='100%'>
                                <tr>
                                  <td>
                                      <select id='show'>
                                        $option
                                        <option value='1'>Todas las novedades</option>
                                        <option value='2'>Solo generadas por mi</option>
                                      </select>
                                  </td>
                                  <td></td>
                              </tr>
                              </table>
                          </fieldset>";

                 $sql = "SELECT a.id_empleado as id_empleado, upper(concat(apellido,', ',nombre)) as emple, a.id, date_format(fecha, '%d/%m/%Y') as fecha, interno, rubro, detalle_anomalia, date_format(a.fecha_alta, '%d/%m/%Y - %H:%i') as generada, time_to_sec(TIMEDIFF(now(), a.fecha_alta)) as segundos, observacion_taller, reparada
                         FROM anomalias a
                         inner join empleados e on e.id_empleado = a.id_empleado
                         inner join rubros_anomalias r on r.id = a.id_rubroanomalia
                         inner join unidades u on u.id = a.id_unidad
                         where activa $show
                         order by a.fecha_alta DESC
                         LIMIT 30";
               //  die($sql);
                 $conn = conexcion();
                 $result = mysql_query($sql, $conn) or die(mysql_error($conn));
                 $tabla.="
                 <table width='100%' id='anomalias' class='table table-zebra'>
                                <thead class='ui-widget ui-widget-header ui-corner-all'>
                                       <tr>
                                           <th>Fecha</th>
                                           <th>Interno</th>
                                           <th>Generada por...</th>
                                           <th>Rubro</th>
                                           <th>Detalle Anomalia</th>
                                           <th>Generada el dia...</th>
                                           <th>Observaciones Taller</th>
                                           <th>Eliminar</th>
                                       </tr>
                                </thead>
                                <tbody>";
                 $i=0;
                 while($data = mysql_fetch_array($result)){
                             $color = "";
                             if ($data[reparada]){
                                $color="#A4FFA4";
                             }
                             else{
                                  if ($i++%2){
                                     $color='#D0D0D0';
                                  }
                                  else{
                                       $color='#FFFFFF';
                                  }
                             }
                             $tabla.="<tr bgcolor='$color'>
                                          <td>$data[fecha]</td>
                                          <td align='right'>$data[interno]</td>
                                          <td>$data[emple]</td>
                                          <td>$data[rubro]</td>
                                          <td width='50%'>$data[detalle_anomalia]</td>
                                          <td>$data[generada]</td>
                                          <td>$data[observacion_taller]</td>";
                             if (($data['segundos'] < 600) && ($data[id_empleado] == $_SESSION[id_chofer]))
                                          $tabla.="<td align='center'><img id='$data[id]' src='../eliminar.png' border='0' width='32' height='33'></td>";
                             else
                                          $tabla.='<td></td>';
                             $tabla.="</tr>";
                 }
                 $tabla.="</tbody>
                          </thead>
                           <script>

                                                $('table tbody tr td img').click(function(){
                                                                                            if (confirm('Confirma eliminar la anomalia?')){
                                                                                               var id = $(this).attr('id');
                                                                                               $.post('updano.php', {accion:'baja', idanom:id}, function(ok){loadAn();});
                                                                                            }
                                                                                            });
                                                $('#show').selectmenu({width: 350});
                                                $('#show').change(function(){
                                                    var sh = $(this).val();
                                                    $('#tabs-2').html(\"<div align='center'><img  alt='cargando' src='../vista/ajax-loader.gif' /></div>\");
                                                    $.post('updano.php', {accion: 'load', s: sh}, 
                                                    function(data){
                                                        $('#tabs-2').html(data);
                                                      });
                                                  });

                           </script>";
                 print $tabla;
  }
  elseif($accion == 'baja'){
             try{
                 update("anomalias", "fecha_baja, activa, cancelo_conductor, id_empleado_baja", "now(), 0, 1, $_SESSION[id_chofer]", "id = $_POST[idanom]");
                 print  json_encode(1);
             } catch (Exception $e) {
                     print json_encode($e->getMessage());
             }
  }
?>

