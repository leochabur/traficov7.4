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
  elseif ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT id, upper(razon_social) as cliente FROM clientes c where id_estructura = $_POST[str] order by razon_social";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="'.$_POST[name].'" name="'.$_POST[name].'" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>$data[cliente]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#$_POST[name]').selectmenu({width: 350});
               </script>";
     mysql_free_result($result);
     mysql_close($conn);
     print $tabla;
  }
  elseif ($accion == 'ldemp'){
     $conn = conexcion();

     $sql = "SELECT id, upper(razon_social) as cliente
             FROM empleadores e
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);

     $tabla= '<select id="'.$_POST[name].'" name="'.$_POST[name].'" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">
              <option value="0">Todos</option>';
     while ($data = mysql_fetch_array($result)){
           $tabla.="<option value='$data[id]'>$data[cliente]</option>";
     }
     $tabla.="
               <script type='text/javascript'>
                                $('#$_POST[name]').selectmenu({width: 350});
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
     $sql = "select concat(apellido,', ', e.nombre), date_format(fservicio, '%d/%m/%Y'), upper(razon_social), sum(km) as km, count(*) as cant, e.id_empleado, interno
             from ordenes o
             inner join unidades u on u.id = o.id_micro
             inner join empleados e on e.id_empleado = o.id_chofer_1
             inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
             where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and e.id_empleador = $_POST[flet] and id_propietario = $_POST[emp]
             group by e.id_empleado, fservicio, c.id, o.id_micro
             order by apellido, fservicio, razon_social";
   //  die($sql);
     $conn = conexcion();
     
     $result = mysql_query($sql, $conn);
     $tabla='<table id="example" name="example" class="ui-widget ui-widget-content" width="75%" align="center">
                    <thead>
                    <tr class="ui-widget-header">
                        <th>Conductor</th>
                        <th>Fecha Servicio</th>
                        <th>Cliente</th>
                        <th>Interno</th>
                        <th>Kilometros</th>
                        <th>Cant. Serv</th>
                    </tr>
                    </thead>
                    <tbody>';
     $data = mysql_fetch_array($result);
     while ($data){
           $emp = $data['id_empleado'];
           $km = 0;
           $ser = 0;
           while (($data) &&($emp == $data['id_empleado'])){
               $tabla.="<tr>
                            <td align='left'>".htmlentities($data[0])."</td>
                            <td align='center'>$data[1]</td>
                            <td align='left'>".htmlentities($data[2])."</td>
                            <td align='right'>$data[interno]</td>
                            <td align='right'>$data[3]</td>
                            <td align='right'>$data[4]</td>
                            </tr>";
               $km+= $data['km'];
               $ser+= $data['cant'];
               $data = mysql_fetch_array($result);
           }
           $tabla.="<tr class='pad'><td colspan='4' align='right'>TOTAL</td>
                         <td align='right'>$km</td>
                         <td align='right'>$ser</td>
                    </tr>
                    <tr><td colspan='6'><hr align='tr'></td></tr>";
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

