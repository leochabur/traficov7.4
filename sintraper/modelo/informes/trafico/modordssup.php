<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldint'){
     $conn = conexcion();

     $sql = "SELECT id, upper(razon_social) as cliente
             FROM clientes c
             where id_estructura = $_POST[str] and activo
             order by cliente";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[0]'>$data[1]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#clientes').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cliente = '';
     if ($_POST['cliente']){
           $cliente = "and (ord.id_cliente = $_POST[cliente])";
     }
     $sql = "select upper(razon_social) as cli, date_format(o.fservicio, '%d/%m/%Y') as fservicio, interno, upper(ori.ciudad), upper(des.ciudad), o.km, comentario , upper(concat(e1.apellido,', ', e1.nombre)) as emple1, id_chofer_1, date_format(o.hcitacion, '%H:%i') as hcitacion, date_format(o.hsalida, '%H:%i') as hsalida, date_format(o.hfinservicio, '%H:%i') as hfinservicio, cita, fina, o.nombre as nomsrv, id_orden
             from (select nombre, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_micro, id_estructura_cliente, comentario, km, o.id as id_orden, id_chofer_1, o.hcitacion, o.hsalida, o.hfinservicio, o.hllegada, id_cliente, fservicio, TIME_TO_SEC(o.hcitacion) as cita, TIME_TO_SEC(o.hfinservicio) as fina
                   from ordenes o
                   where (fservicio between '$desde' and '$hasta') and (id_estructura = $_POST[str]) and (not borrada) and (not suspendida) and  (id_chofer_1 is not null)
                   union all
                   select nombre, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_micro, id_estructura_cliente, comentario, km, o.id as id_orden, id_chofer_2, o.hcitacion, o.hsalida, o.hfinservicio, o.hllegada, id_cliente, fservicio, TIME_TO_SEC(o.hcitacion) as cita, TIME_TO_SEC(o.hfinservicio) as fina
                   from ordenes o
                   where (fservicio between '$desde' and '$hasta') and (id_estructura = $_POST[str]) and (not borrada) and (not suspendida) and (id_chofer_2 is not null)

             ) o
             left join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
             left join unidades u on (u.id = o.id_micro)
             inner join ciudades ori on ori.id = id_ciudad_origen and ori.id_estructura = id_estructura_ciudad_origen
             inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
             inner join empleados e1 on e1.id_empleado = o .id_chofer_1
             order by o.id_chofer_1, fservicio, hcitacion";
    // die($sql);

     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="100%" align="center">
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $cond = $data['id_chofer_1'];
           $header='<tr>
                        <th colspan="6" class="ui-widget-header">'.htmlentities($data['emple1']).'</th>
                    </tr>
                    <tr class="ui-widget-header">
                        <th>Cliente</th>
                        <th>Fecha Servicio</th>
                        <th>Servicio</th>
                        <th>H Citacion</th>
                        <th>H Salida</th>
                        <th>H Finzalizacion</th>
                    </tr>';
           $fec = '';
           $body='';
           $ok=false;
           while (($data) &&($cond == $data['id_chofer_1'])){
                 $fec = $data['fservicio'];
                 $ult = -1;
                 while (($data) && ($cond == $data['id_chofer_1']) && ($fec == $data['fservicio'])){
                       if ($ult <> -1){
                          if ($data['cita'] < $ult){
                             $body.="<tr>
                                      <td>$ul_cli</td>
                                      <td>$data[fservicio]</td>
                                      <td>$ul_serv</td>
                                      <td><div class='hora' id='hcitacion-$ul_id'>$ult_cita</div></td>
                                      <td><div class='hora' id='hsalida-$ul_id'>$ult_sale</div></td>
                                      <td><div class='hora' id='hfinservicio-$ul_id'>$ult_fina</div></td>
                                      </tr>
                                      <tr>
                                      <td>$data[cli]</td>
                                      <td>$data[fservicio]</td>
                                      <td>$data[nomsrv]</td>
                                      <td><div class='hora' id='hcitacion-$data[id_orden]'>$data[hcitacion]</div></td>
                                      <td><div class='hora' id='hsalida-$data[id_orden]'>$data[hsalida]</div></td>
                                      <td><div class='hora' id='hfinservicio-$data[id_orden]'>$data[hfinservicio]</div></td>
                                      </tr>";
                                      $ok=true;
                          }
                       }
                       $ult = $data['fina'];
                       $ult_cita = $data['hcitacion'];
                       $ult_sale = $data['hsalida'];
                       $ult_fina = $data['hfinservicio'];
                       $ul_cli = $data['cli'];
                       $ul_serv = $data['nomsrv'];
                       $ul_id = $data['id_orden'];
                       $data = mysql_fetch_array($result);
                 }
           }
           if ($ok){
              $tabla.=$header.$body;
           }
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
                           $.editable.addInputType('masked', {
                                                             element : function(settings, original) {
                                                                                                    var input = $('<input />').mask(settings.mask);
                                                                                                    $(this).append(input);
                                                                                                    return(input);
                                                                                                    }
                                                             });
                           $.mask.definitions['~']='[012]';
                           $.mask.definitions['%']='[012345]';
                           $('.hora').editable('/modelo/procesa/upd_ordenes.php', {type:'masked', mask: '~9:%9'});
                   </script>";
    print $tabla;
  }
  
?>

