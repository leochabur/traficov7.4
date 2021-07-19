<?
  session_start();
     set_time_limit(0);
     error_reporting(0);
   // date_default_timezone_set('America/New_York');
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include_once('../../controlador/ejecutar_sql.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'load'){
     $conn = conexcion();
     $origen = ($_POST['ori']?"WHERE (id = $_POST[ori]) and (id_estructura = $_SESSION[structure])":"");
     $destino = ($_POST['des']?"WHERE (id = $_POST[des]) and (id_estructura = $_SESSION[structure])":"");

     $sql="SELECT d.id, ori.ciudad, des.ciudad, distancia, time_format(SEC_TO_TIME(tiempo),'%H:%i')
           FROM distanciasRecorridos d
           inner join (select * from ciudades $origen) ori on ori.id = d.id_origen and ori.id_estructura = d.id_estructura_origen
           inner join (select * from ciudades $destino) des on des.id = d.id_destino and des.id_estructura = d.id_estructura_destino
           order by ori.ciudad, des.ciudad";


     $result = mysql_query($sql, $conn);

     $data = mysql_fetch_array($result);
     $tabla.= "<table width='100%' class='order' id='recos'>
                      <thead>
                                <tr>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>KM</th>
                                    <th>Tiempo Viaje</th>
                                    <th>Aplicar Inversa</th>
                                    <th></th>
                                </tr>
                           </thead>
                           <tbody>";
     $j=0;
     while ($data){
           $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
           $tabla.="<tr bgcolor='$color'>
                        <td width='30%'>".htmlentities($data[1])."</td>
                        <td width='30%'>".htmlentities($data[2])."</td>
                        <td align='center'><input type='text' size='5' value='$data[3]' id='km-$data[id]' style='text-align:right'></td>
                        <td align='center'><input type='text' size='5' value='$data[4]' id='tpo-$data[id]' style='text-align:right' class='hora'></td>
                        <td align='center'><input type='checkbox' id='vta-$data[id]' checked></td>
                        <td><input type='button' value='Guardar' id='$data[0]'></td>
                    </tr>";
           $j++;
           $data = mysql_fetch_array($result);
     }
     $tabla.="</tbody></table><br>";

     $tabla.="<style type='text/css'>
                     table.order {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 8pt;
	                              text-align: left;
                               }
                     table.order thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.order tbody td {
	                                        color: #3D3D3D;
	                                        padding: 4px;
	                                        vertical-align: top;
                                         }
                     td.click, th.click{
                                        background-color: #bbb;
                                        }
                     td.hover, tr.hover{
                                        background-color: #69f;
                                        }
                     th.hover, tfoot td.hover{
                                              background-color: ivory;
                                              }
                     td.hovercell, th.hovercell{
                                                background-color: #abc;
                                                }
                     td.hoverrow, th.hoverrow{
                                              background-color: #6df;
                                              }
              </style>
               <script type='text/javascript'>

             		            $.mask.definitions['~']='[012]';
                                $.mask.definitions['%']='[012345]';
                                $('.hora').mask('~9:%9');
                                $('.order').tableHover();
                                $('#recos input:button').button().click(function(){
                                                                                  var id = $(this).attr('id');
                                                                                  var tpo = $('#tpo-'+id).val();
                                                                                  var km = $('#km-'+id).val();
                                                                                  var vta = 0;
                                                                                  if($('#vta-'+id).is(':checked')) {
                                                                                      vta = 1;
                                                                                  }
                                                                                  $.post('/modelo/ordenes/modkmtpo.php',
                                                                                         {accion:'upd', t:tpo, k:km, i:id, v:vta},
                                                                                         function(data){
                                                                                                        var response = $.parseJSON(data);
                                                                                                        if (response.status){
                                                                                                           alert('Se han modificado con exito los parametros');
                                                                                                        }
                                                                                                        else{
                                                                                                             alert('NO se han podido modificado los parametros!!');
                                                                                                        }
                                                                                                        });
                                                                                  });
               </script>";
     print $tabla;
  }
  elseif ($accion == 'upd'){
         $conn = conexcion();
         $sql = "update distanciasRecorridos set distancia = $_POST[k], tiempo = time_to_sec('$_POST[t]') where id = $_POST[i]";
         $response = array();     ///respuesta al cliente de la accion requerida
         $response[status] = true;
         try{
              begin($conn);
              ejecutarSQL($sql, $conn);
              if ($_POST[v]){
                 $sql = "SELECT id_origen, id_estructura_origen, id_destino, id_estructura_destino FROM distanciasRecorridos where id = $_POST[i]";
                 $result = ejecutarSQL($sql, $conn);
                 if ($data = mysql_fetch_array($result)){
                    $sql = "update distanciasRecorridos
                            set distancia = $_POST[k], tiempo = time_to_sec('$_POST[t]')
                            where id_origen = $data[id_destino] and id_estructura_origen = $data[id_estructura_destino] and
                                  id_destino = $data[id_origen] and id_estructura_destino = $data[id_estructura_origen]";
                    ejecutarSQL($sql, $conn);
                 }
              }
              commit($conn);
              cerrarconexcion($conn);
              $response[msge] = "Se ha modificado con exito el recorrido";
              print (json_encode($response));
         }catch (Exception $e) {
                                rollback($conn);
                                cerrarconexcion($conn);
                                $response[status] = false;
                                $response[msge]=$e->getMessage();
                                print (json_encode($response));
                                };
  }
  elseif ($accion == 'sver'){
         $conn = conexcion();
         $sql = "INSERT INTO distanciasRecorridos (id_origen, id_estructura_origen, id_destino, id_estructura_destino, distancia, tiempo)
                             VALUES ($_POST[or], $_SESSION[structure], $_POST[de], $_SESSION[structure], $_POST[km], time_to_sec('$_POST[to]'))
                             ON DUPLICATE KEY UPDATE distancia = $_POST[km], tiempo = time_to_sec('$_POST[to]')";
         $response = array();     ///respuesta al cliente de la accion requerida
         $response[status] = true;
         try{
              begin($conn);
              ejecutarSQL($sql, $conn);
              commit($conn);
              cerrarconexcion($conn);
              $response[msge] = "<font color='#00FF00'><b>Se ha creado con exito el recorrido seleccionado</b></font>";
              print (json_encode($response));
         }catch (Exception $e) {
                                rollback($conn);
                                cerrarconexcion($conn);
                                $response[status] = false;
                                $response[msge]="<font color='#FF0000'><b>No se ha podido cargar el recorrido</b></font>";
                                print (json_encode($response));
                                };
  }
  
?>

