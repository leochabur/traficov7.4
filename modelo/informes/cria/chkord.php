<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT upper(razon_social) as nombre,  id
             FROM clientes c
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>".htmlentities($data[0])."</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#clientes').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'res'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $struct = '';
     $cliente = '';

     $str = $_POST['str'];
     
     if ($_POST['clientes']){
        $cliente = "and (o.id_cliente = $_POST[clientes])";
     }

     
     $fhora="";
     if ($_POST['chk'] == 'chkfh'){
        $fhora = "and (horastd < fchequeo)";
     }
     
     $llt = "";
     if ($_POST['llp'] == 'llfh'){
        $llt = "and (horastd < horareal)";
     }
     
     $rptas="";
     if ($_POST['rpta'] == 'rtan'){
        $rptas = "WHERE (not valor)";
     }
     
     /*$sql = "SELECT date_format(fservicio, '%d/%m/%Y') as fservicio, apenom, razon_social, nombre, hcitacion, hsalida, date_format(hora_chequeo, '%d/%m/%Y %H:%i') as fchequeo
             FROM chequeo_ordenes ch
             inner join respuestas_chequeo r on r.id_chequeo = ch.id
             inner join ordenes o on o.id = ch.id_orden and o.id_estructura = ch.id_estructura_orden
             inner join clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
             inner join usuarios u on u.id = ch.id_user
             where (fservicio between '$desde' and '$hasta') $struct $cliente $fhora $rptas $llt    */

      $sql ="SELECT fservicio, apenom, razon_social, nombre, time_format(hcitacion,'%H:%i') as citacion, time_format(horastd, '%H:%i') as hdiagrama,
                    time_format(horareal, '%H:%i') as hreal, fchequeo, id_orden, interno
             FROM(
                  SELECT date_format(fservicio, '%d/%m/%Y') as fservicio, apenom, razon_social, nombre, o.hcitacion,
                         concat(fservicio,' ',if (s.i_v = 'i', o.hllegada, o.hsalida)) as horastd,
                         date_format(hora_chequeo, '%d/%m/%Y %H:%i') as fchequeo,
                         concat(fservicio,' ',if (s.i_v = 'i', o.hllegadaplantareal, o.hsalidaplantareal)) as horareal, hora_chequeo, o.id as id_orden, interno
                  FROM chequeo_ordenes ch
                  inner join (select * from respuestas_chequeo $rptas) r on r.id_chequeo = ch.id
                  inner join (select * from ordenes o where (not borrada) and (not suspendida) and (fservicio between '$desde' and '$hasta') and (o.id_estructura = $str) $cliente) o on o.id = ch.id_orden and o.id_estructura = ch.id_estructura_orden
                  inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
                  inner join clientes c ON ((c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente))
                  inner join usuarios u on u.id = ch.id_user
                  left join unidades un on un.id = o.id_micro
             ) o
             where (true) $llt $fhora


             ";
   //  die($sql);
     $conn = conexcion();
/*     $res_km = mysql_query($sql_km, $conn);
     if ($row = mysql_fetch_array($res_km)){
        $km = $row['km'];
     }    */
     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <thead>
                    <tr class="ui-widget-header">
                        <th id="razon_social">Fecha</th>
                        <th id="interno">Usuario</th>
                        <th id="fservicio">Cliente</th>
                        <th id="nombre">Servicio</th>
                        <th id="nombre">Interno</th>
                        <th id="hsalida">H. Diagrama</th>
                        <th id="hsalida">H. Real</th>
                        <th id="km">Hora Chequeo</th>
                    </tr>
                    </thead>
                    <tbody>';

     while ($data = mysql_fetch_array($result)){
                 $fondo = ((($i++)%2)==0)?"par":"impar";
                 $tabla.="<tr class='$fondo' id='$data[id_orden]'>
                              <td align='left'>$data[0]</td>
                              <td align='left'>$data[1]</td>
                              <td align='left'>$data[2]</td>
                              <td align='left'>".htmlentities($data[3])."</td>
                              <td align='center'>$data[interno]</td>
                              <td align='center'>$data[5]</td>
                              <td align='right'>$data[6]</td>
                              <td align='right'>$data[7]</td>
                          </tr>";
     }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example th{
                                padding:13px;
                                font-size: 82.5%;
                                }
                         #example tr{
                                padding:13px;
                                font-size: 80.5%;
                                }
#example tbody tr:nth-child(odd){
    background: #D0D0D0;

}

#example tbody tr:nth-child(even){
    background: #FFFFFF;

}

                         #example tbody tr:hover {

                                        background-color: #FF8080;
                                        }
                  </style>
                  <script type="text/javascript">
                          $("#example tbody tr").click(function(){
                                                                  var id = "orden-"+$(this).attr("id");

                                                                  var serv = $(this).find("td:eq(3)").html();
                                                                  var int = $(this).find("td:eq(4)").html();
                                                                  var hsalida = "";
                                                                  var hllegada = "";
                                                                  var dialog = $(\'<div style="display:none" id="dialog" class="loading" align="center"></div>\').appendTo(\'body\');
                                                                  dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: \'Chequear orden\',
                                                                                   width:850,
                                                                                   height:600,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: \'blind\',
                                                                                                duration: 250
                                                                                         },
                                                                                         hide: {
                                                                                               effect: \'blind\',
                                                                                               duration: 250
                                                                                               }
                                                                                   });
                                                                                   dialog.load(\'/vista/ordenes/checkord.php\',{orden:id, srv:serv, cche:int},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass(\'loading\');});

                                                                    });



                  </script>
';
    print $tabla;
  }
  
?>

