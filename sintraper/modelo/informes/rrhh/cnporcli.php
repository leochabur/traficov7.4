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

     if ($_POST['str']){
        $struct = "and (o.id_estructura = $_POST[str])";
        if ($_POST['cli']){
           $cliente = "and (o.id_cliente = $_POST[cli]) and (o.id_estructura_cliente = $_POST[str])";
        }
     }
     else{
          if (isset($_POST['cli'])){
             if ($_POST['cli']){
                $cliente = "and (o.id_cliente = $_POST[cli]) and (o.id_estructura_cliente = $_POST[str])";
             }
          }
     }
     $sql = "select upper(razon_social), cuit, upper(concat(apellido,', ',nombre)), nrodoc, count(*) as cant_serv, legajo
from(
SELECT id_chofer_1, id_cliente, id_estructura_cliente, id_micro
FROM  ordenes o
where (fservicio between '$desde' and '$hasta') $struct $cliente and id_chofer_2 is null
union all
SELECT id_chofer_2, id_cliente, id_estructura_cliente, id_micro
FROM  ordenes o
where (fservicio between '$desde' and '$hasta') $struct $cliente and id_chofer_2 is not null) o
inner join empleados e on e.id_empleado = o.id_chofer_1
inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
group by id_cliente, id_chofer_1
order by razon_social, apellido, nombre";

//die($sql);

     $conn = conexcion();
/*     $res_km = mysql_query($sql_km, $conn);
     if ($row = mysql_fetch_array($res_km)){
        $km = $row['km'];
     }    */
     $result = mysql_query($sql, $conn);
     $tabla='<a href="#?des='.$desde.'&has='.$hasta.'&cli='.$cliente.'&str='.$struct.'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     while ($data){
           $cliente = $data['0'];
           $i = 0;
           $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social" colspan="6">'.$data[0].'</th>
                    </tr>
                    <tr class="ui-widget-header">
                        <th>Legajo</th>
                        <th>Apellido, Nombre</th>
                        <th>Cantidad Servicios</th>
                    </tr>                    ';
           while (($data)&&($cliente == $data['0'])){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>$data[5]</td>
                              <td align='left'>".htmlentities($data[2])."</td>
                              <td align='right'>$data[4]</td>
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

