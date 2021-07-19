<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include_once ('../../controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../modelo/enviomail/sendordbjafn.php');

$accion = $_POST['accion'];
if ($accion == 'load'){

  $id = $_POST['nroorden'];
  $fecha = dateToMysql($_POST['fservicio'], '/');
  $sql="SELECT gl.id, detalle, round(importe,2)
        FROM liquidacion_servicio_turismo l
        inner join gastos_liquidacion gl on gl.id_liquidacion_gasto = l.id
        inner join items_gastos_turismo ig on ig.id = gl.id_item_gasto
        where l.id_orden = $_POST[orden] and l.id_conductor = $_POST[cond]";
        
  $tabla="";
  $result = ejecutarSQL($sql);
  $suma=0;
  $i=1;
  while ($row = mysql_fetch_array($result)){
        $tabla.="<tr>
                     <td align='center'><input type='text' size='20' value='$row[1]' readonly class='ui-widget-content ui-corner-all'></td>
                     <td align='center'><input type='text' size='20' value='$row[pooo]' readonly class='ui-widget-content ui-corner-all'></td>
                     <td align='center'><input STYLE='text-align:right' type='text' size='20' value='$row[2]' readonly class='monto ui-widget-content ui-corner-all'></td>";
        if (!$_POST[status])
                     $tabla.="<td align='center'><img class='delete' id='$row[0]' src='../../../vista/menos.png' width='20' height='20' border='0'></td>";
        else
            $tabla.="<td></td>";
        $tabla.="</tr>";
        $suma+=$row[2];
  }
  if (true){
     $tabla.='<tr>
                  <td></td>
                  <td align="right"><b>Total de gastos</b></td>
                  <td align="center"><input STYLE="text-align:right" type="text" size="20" value="'.number_format($suma,2).'" readonly class="monto ui-widget-content ui-corner-all"></td>
                  <td></td>
              </tr>
              <tr>
                  <td></td>
                  <td align="right"><b>Entregado a Rendir</b></td>
                  <td align="center"><input STYLE="text-align:right" type="text" size="20" value="'.number_format($_POST[entregado],2).'" readonly class="monto ui-widget-content ui-corner-all"></td>
                  <td></td>
              </tr>
              <tr>
                  <td></td>
                  <td align="right"><b>Saldo liquidacion</b></td>
                  <td align="center"><input STYLE="text-align:right" type="text" size="20" value="'.number_format(($_POST[entregado] - $suma),2).'" readonly class="monto ui-widget-content ui-corner-all"></td>
                  <td></td>
              </tr>
              <script>
                      $(".delete").click(function(){
                                                    var id_g = $(this).attr("id");
                                                    var del = $(this);
                                                    del.hide();
                                                    if (confirm("Seguro eliminar el item?")){
                                                       $.post("/modelo/ordenes/uplqvje.php",{accion:"delete", gasto:id_g},function(data){
                                                                                                                                         $.post("/modelo/ordenes/uplqvje.php",{accion:"load",
                                                                                                                                                                               orden:'.$_POST[orden].',
                                                                                                                                                                               cond:'.$_POST[cond].',
                                                                                                                                                                               entregado:'.$_POST[entregado].',
                                                                                                                                                                               status:'.$_POST[status].'
                                                                                                                                                                                                 },function(data){
                                                                                                                                                                                                             $("#body").html(data);
                                                                                                                                                                                                             del.remove();
                                                                                                                                                                                                             });
                                                                                                                                         });
                                                    }
                                                    });
              </script>';
  }
  print $tabla;
}
elseif($accion == 'delete'){
               delete('gastos_liquidacion', 'id', "$_POST[gasto]");
}
elseif($accion == 'add'){
               try{
               $sql = "SELECT id FROM liquidacion_servicio_turismo where id_orden = $_POST[orden]";
               $result = ejecutarSQL($sql);
               if ($row = mysql_fetch_array($result)){
                  $liq = $row[0];
               }
               else{
                    $liq = insert("liquidacion_servicio_turismo", "id, id_orden, id_conductor, entregado_a_rendir", "$_POST[orden], $_POST[emple], $_POST[entr]");
               }
               insert("gastos_liquidacion", "id, id_item_gasto, id_liquidacion_gasto, importe", "$_POST[type], $liq, $_POST[monto]");
               }
     catch (Exception $e) {print $e->getMessage();};
}
elseif($accion == 'oplq'){
               $response = array();     ///respuesta al cliente de la accion requerida
               try{
                    $liq = insert("liquidacion_servicio_turismo", "id, id_orden, id_conductor, entregado_a_rendir, cerrada, usuario_abre, fecha_abre",
                                  "$_POST[orden], $_POST[cond], $_POST[monto], 0, $_SESSION[userid], now()");
                    $response[status] = true;
                    $response[msge] = "Se ha abierto la liquidacion con exito!!";
               }catch (Exception $e){
                                     $response[status] = false;
                                     $response[msge] = $e->getMessage();
                                     };
               print json_encode($response);
}
elseif($accion == 'close'){
               $response = array();     ///respuesta al cliente de la accion requerida
               try{
                    $liq = update("liquidacion_servicio_turismo",
                                  "cerrada, usuario_cierre, fecha_cierre",
                                  "1, $_SESSION[userid], now()",
                                  "id = $_POST[liq]");
                    $response[status] = true;
                    $response[msge] = "Se ha cerrado la liquidacion con exito!!";
               }catch (Exception $e){
                                     $response[status] = false;
                                     $response[msge] = $e->getMessage();
                                     };
               print json_encode($response);
}

?>

