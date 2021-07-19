<?
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../../controlador/bdadmin.php');
  include ('../../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];
  if ($accion == 'ldint'){
     $conn = conexcion();

     $sql = "SELECT id, interno
             FROM unidades
             where id_estructura = $_POST[str]
             order by interno";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="internos" name="internos" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>$data[interno]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#internos').selectmenu({width: 100});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif($accion == 'reskm'){
     $rango = "$_POST[desde]  hasta  $_POST[hasta]";
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $interno = '';
     if ($_POST['internos']){
           $interno = "and (ord.id_micro = $_POST[internos])";
     }
     $sql = "select interno, date_format(fservicio, '%d/%m/%Y') as fservicio, date_format(hcitacion, '%H:%i') as hsalida, date_format(hfinservicio, '%H:%i') as hfin, upper(nombre) as reco, upper(razon_social) as cli, km
             from (select fservicio, id_cliente, id_estructura_cliente, id_micro, km, nombre, hcitacion, hfinservicio
                   from ordenes ord
                   where (ord.fservicio between '$desde' and '$hasta') and (ord.id_estructura = $_POST[str]) and (not suspendida) and (not borrada) $interno
             ) o
             inner join clientes c on (o.id_cliente = c.id) and (o.id_estructura_cliente = c.id_estructura)
             inner join unidades m on m.id = o.id_micro
             order by interno, fservicio, razon_social, hcitacion
             ";
             
     $res_sql = "SELECT sum(km) as km, id_micro
                 FROM ordenes o
                 where (fservicio between '$desde' and '$hasta') and (o.id_estructura = $_POST[str]) $interno
                 group by o.id_micro";
     $conn = conexcion();

     $res_result = mysql_query($res_sql, $conn);
     $tot_km = array();
     while ($row = mysql_fetch_array($res_result)){
           $tot_km[$row['id_micro']]=$row['km'];
     }
     
     $result = mysql_query($sql, $conn);
     $tabla='<a href="/modelo/informes/export.php?sql=\"'.$sql.'\"">s</a>
             <table id="example" name="example" class="ui-widget ui-widget-content" width="75%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Interno</th>
                        <th>Fecha Servicio</th>
                        <th>Hora Inicio</th>
                        <th>Hora Finalizacion</th>
                        <th>Recorrido</th>
                        <th>Cliente</th>
                        <th>Kilometros</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $int = $data['interno'];
           $tot = 0;
           $tabla.="<tr class='pad'><td colspan='7' align='center'><b><u>Detalle servicios realizados por el interno $int</u></b></td>
                         <td></td>
                    </tr>";
           while (($data) &&($int == $data['interno'])){
               $por = round((($data[km]/$tot_km[$data[id_micro]])*100),4);
               $tabla.="<tr>
                            <td align='center'>$data[0]</td>
                            <td align='right'>$data[1]</td>
                            <td align='center'>$data[2]</td>
                            <td align='center'>$data[3]</td>
                            <td align='left'>".htmlentities($data[4])."</td>
                            <td align='center'>$data[5]</td>
                            <td align='right'>$data[6]</td>
                            </tr>";
               $tot=$tot + $data['km'];
               $data = mysql_fetch_array($result);
           }
           $tabla.="<tr class='pad'><td colspan='7' align='right'><b><u>TOTAL KM INTERNO $int:  $tot km.</u></b></td>
                         <td></td>
                    </tr>
                    <tr><td colspan='7'><hr align='tr'></td></tr>";
     }
     $tabla.='</tbody>
              </table>
                  <style>
                         #example th{
                                padding:13px;
                                font-size: 82.5%;
                                }
                         #example tr{
                                padding:13px;
                                font-size: 92.5%;
                                }
                         .pad{
                                padding:10px;
                                font-size: 85%;
                                }
                         #example tbody tr:hover {

                                        background-color: #FF8080;

}
</style>
                  </style>';
    print $tabla;
  }
  
?>

