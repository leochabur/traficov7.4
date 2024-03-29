<?php
  session_start();
  ////////////////// modulo para dar de alta y mdificar un conductore en la BD  /////////////////////
    include ('../../controlador/bdadmin.php');
  include ('../../controlador/ejecutar_sql.php');
  include_once ('../../modelo/utils/dateutils.php');
  include_once ('../../modelo/enviomail/sendmail.php');
  
  $accion = $_POST['accion'];

  if($accion == 'sveanom'){ //codigo para guardar una anomalia
             $fecha = dateToMysql($_POST['fecha'], '/');

             $id_cliente = $id_str_cliente = 'NULL';
             if ($_POST['tipo'] == 6)
             {
                 $id_cliente = ($_POST['cliente'] ? $_POST['cliente']: 'NULL');
                 $id_str_cliente = ($_POST['cliente'] ? $_SESSION['structure']: 'NULL');
             }
             try{
                 $ok = insert('anomalias', 'id, id_usuario_alta, causa, id_unidad, id_rubroanomalia, fecha, detalle_anomalia, fecha_alta, id_cliente, id_estructura_cliente',
                              "$_SESSION[userid], $_POST[origen], $_POST[internos], $_POST[tipo], '$fecha', '$_POST[desc]', now(), $id_cliente, $id_str_cliente"); //agrega un conductor pendiente de procesamiento por parte del sector de RRHH
                 $sql = "SELECT date_format(fecha, '%d/%m/%Y'), interno, upper(origen), upper(apenom), upper(detalle_anomalia), u.interno
                         FROM anomalias a
                         inner join origen_anomalias o on o.id = a.causa
                         inner join unidades u on u.id = a.id_unidad
                         inner join usuarios us on us.id = a.id_usuario_alta
                         where a.id = (select max(id) from anomalias)";
                 $result = ejecutarSQL($sql);

                 if ($data = mysql_fetch_array($result)){
                    $cuerpo="Se ha generado una nueva anomalia el dia: $data[0], originada por: $data[2]<br>
                             Usuario: $data[3]<br>
                             Interno: $data[interno]<br>
                             Detalle: $data[4]";
                    if ($_SESSION['userid'] != 17)
                    {
                      enviarMail("leochabur@gmail.com,ogurevich@masterbus.net,nfillopski@masterbus.net", $cuerpo, "Nueva Anomalia Generada");
                    }
                 }
                 print  json_encode(array('ok' => true));
             } catch (Exception $e) {
                     print json_encode(array('ok' => false, 'message' => $e->getMessage()));
             }
  }
  elseif($accion == 'load'){
                 $sql = "SELECT a.id, date_format(fecha, '%d/%m/%Y') as fecha, interno, rubro, detalle_anomalia, date_format(fecha_alta, '%d/%m/%Y - %H:%i') as generada, time_to_sec(TIMEDIFF(now(), fecha_alta)) as segundos, observacion_taller, reparada
                         FROM anomalias a
                         inner join rubros_anomalias r on r.id = a.id_rubroanomalia
                         inner join unidades u on u.id = a.id_unidad
                         where id_empleado = $_SESSION[id_chofer] and activa
                         order by fecha_alta";
               //  die($sql);
                 $conn = conexcion();
                 $result = mysql_query($sql, $conn);
                 $tabla="<div style='width:200px; background-color:#A4FFA4'>Anomalias reparadas</div><table width='100%' id='anomalias'>
                                <thead class='ui-widget ui-widget-header ui-corner-all'>
                                       <tr>
                                           <th>Fecha</th>
                                           <th>Interno</th>
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
                                          <td>$data[rubro]</td>
                                          <td>$data[detalle_anomalia]</td>
                                          <td>$data[generada]</td>
                                          <td>$data[observacion_taller]</td>";
                             if ($data['segundos'] < 600)
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

