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

     $sql = "select upper(c.nombre) as nombre, count(*), precio_unitario, round((count(*) * precio_unitario),2)
             from ordenes o
             inner join (select * from servicios where id_estructura = $_SESSION[structure]) s on s.id = o.id_servicio
             inner join (select * from cronogramas where id_estructura = $_SESSION[structure]) c on c.id = s.id_cronograma
             where fservicio between '$desde' and '$hasta' and o.id_estructura = $_SESSION[structure] and c.id_cliente = $_POST[cli]
             group by c.id
             union all
             select concat(upper(nombre),' (SERVICIO EVENTUAL)'), 0, 0, 0
             from ordenes o
             where fservicio between '$desde' and '$hasta' and o.id_estructura = $_SESSION[structure] and id_cliente = $_POST[cli] and id_servicio is null
             order by nombre";

     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Servicio</th>
                        <th id="interno">Cant. Serv.</th>
                        <th id="fservicio">Precio Unitario</th>
                        <th id="nombre">Total</th>
                    </tr>';
     $tota=0;
     while ($data){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>".htmlentities($data[0])."</td>
                              <td align='right'>$data[1]</td>
                              <td align='right'>$data[2]</td>
                              <td align='right'>$data[3]</td>
                          </tr>";
                 $tota+= $data[3];
                 $data = mysql_fetch_array($result);
                 $i++;
     }
     $tabla.='<tr><td colspan="3">Importe Total</td><td align="right">$ '.$tota.'</td></tr></tbody>
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

