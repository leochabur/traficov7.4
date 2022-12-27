<?php
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

     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
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
  elseif($accion == 'reskm'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $struct = '';
     $cliente = '';
     if($_POST['type']=="or"){
        $ordenes = "and (not o.borrada) and (not o.suspendida)";
     }
     elseif($_POST['type']=="os"){
        $ordenes = "and (o.suspendida)";
     }
     elseif($_POST['type']=="ob"){
        $ordenes = "and (o.borrada)";
     }
     elseif($_POST['type']=="to"){
        $ordenes = "and (not o.borrada)";
     }
     if ($_POST['str']){
        $struct = "and (o.id_estructura = $_POST[str])";
        if ($_POST['cli']){
           $cliente = "and (o.id_cliente = $_POST[cli])";
        }
     }
     else
     {
          if (isset($_POST['cli'])){
             if ($_POST['cli']){
                $cliente = "and (o.id_cliente = $_POST[cli]) and (o.id_estructura_cliente = $_POST[str])";
             }
          }
     }

     if ($_POST['str'] && (in_array($_POST['str'], array(2,11))))
     {

        $sql = "SELECT upper(razon_social) as razon_social, interno, date_format(hhs.citacion,'%d/%m/%Y'), nombre, date_format(salida, '%H:%i') as salida, km, cod_servicio
                FROM ordenes o
                INNER JOIN horarios_ordenes_sur hhs ON hhs.id_orden = o.id AND hhs.id_estructura_orden = o.id_estructura
                inner join clientes cli on (cli.id = o.id_cliente) and (cli.id_estructura = o.id_estructura_cliente)
                LEFT JOIN unidades m ON (m.id = o.id_micro)
                where (date(citacion) between '$desde' and '$hasta') $struct $cliente $ordenes
                order by razon_social, date(hhs.citacion), time(hhs.citacion)";
      }
      else
      {
           $sql = "SELECT upper(razon_social) as razon_social, interno, date_format(fservicio,'%d/%m/%Y'), nombre, hsalida, km
                   FROM ordenes o
                   inner join clientes cli on (cli.id = o.id_cliente) and (cli.id_estructura = o.id_estructura_cliente)
                   LEFT JOIN unidades m ON (m.id = o.id_micro)
                   where (fservicio between '$desde' and '$hasta') $struct $cliente $ordenes
                   order by razon_social, fservicio, hsalida";
      }

     $conn = conexcion();
/*     $res_km = mysql_query($sql_km, $conn);
     if ($row = mysql_fetch_array($res_km)){
        $km = $row['km'];
     }    */
     $result = mysql_query($sql, $conn);
     $tabla='<a href="/modelo/informes/trafico/exportkmcli.php?des='.$desde.'&has='.$hasta.'&cli='.$cliente.'&str='.$struct.'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     while ($data){
           $cliente = $data['0'];
           $i = 0;
           $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Cliente</th>
                        <th id="interno">Interno</th>
                        <th id="fservicio">Fecha Servicio</th>
                        <th id="nombre">Servicio</th>
                        <th id="hsalida">Horario</th>
                        <th id="km">km</th>
                        <th></th>
                    </tr>';
           while (($data)&&($cliente == $data['0'])){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>$data[0]</td>
                              <td align='center'>$data[1]</td>
                              <td align='center'>$data[2]</td>
                              <td align='left'>".htmlentities($data[3])."</td>
                              <td align='center'>$data[4]</td>
                              <td align='right'>$data[5]</td>
                              <td align='right'>$data[cod_servicio]</td>
                          </tr>";
                 $data = mysql_fetch_array($result);
                 $i++;
           }
           $tabla.="<tr><td colspan='6'><hr align='tr'></td></tr>";
     }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">

                  </script>
';
    print $tabla;
  }
  
?>

