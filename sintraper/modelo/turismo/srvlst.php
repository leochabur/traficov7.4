<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include_once($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include ('../../modelo/utils/dateutils.php');
  $accion= $_POST['accion'];

  if ($accion == 'ldcli'){
     $conn = conexcion();

     $sql = "SELECT upper(razon_social) as nombre,  id
             FROM clientes c
             where id_estructura = $_POST[str]
             order by razon_social";
     $result = mysql_query($sql, $conn);
     if (isset($_POST['all']))
     $tabla= '<select id="clientes" name="clientes" title="Please select something!"  class="ui-widget-content  ui-corner-all"  validate="required:true">';
     else
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
  elseif($accion == 'ldsrv'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cond = '';

     if ($_POST['str']){
        $cond = "(o.id_estructura = $_POST[str])";
     }
     if ($_POST['cli']){
        $cond.= "and (o.id_cliente = $_POST[cli])";
     }

     $sql = "SELECT razon_social, date_format(fservicio, '%d/%m/%Y'), round(precio_venta_neto,2), round(precio_venta_final,2), round(viaticos,2), km, concat(ori.ciudad,' - ', des.ciudad), o.id
             FROM (SELECT * FROM ordenes WHERE (fservicio between '$desde' and '$hasta') and (id_claseservicio = 4) and (not borrada)) o
             inner join ciudades ori on ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
             inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
             left join ordenes_turismo ot on o.id = ot.id_orden and o.id_estructura = ot.id_estructura_orden
             inner join clientes c on c.id = o.id_cliente and o.id_estructura = o.id_estructura_cliente
             where  $cond
             order by razon_social, fservicio";

             //die($sql);
     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     while ($data){
           $cliente = $data['0'];
           $i = 0;
           $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Cliente</th>
                        <th id="interno">Fecha Servicio</th>
                        <th>Origen-Destino</th>
                        <th id="fservicio">Precio Neto</th>
                        <th id="nombre">Precio Final</th>
                        <th id="hsalida">Viaticos</th>
                        <th id="km">km</th>
                    </tr>';
           while (($data)&&($cliente == $data['0'])){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'><a href='../../vista/ordenes/ordtur.php?otmo=$data[7]'>$data[0]</a></td>
                              <td align='center'>$data[1]</td>
                              <td align='left'>".utf8_decode($data[6])."</td>
                              <td align='right'>$data[2]</td>
                              <td align='right'>$data[3]</td>
                              <td align='right'>$data[4]</td>
                              <td align='right'>$data[5]</td>
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
  elseif($accion == 'ldpgs'){
           $sql="SELECT  o.id as id_orden_turismo, date_format(fservicio, '%d/%m/%Y') as fecha,
        concat(ori.ciudad,' - ', des.ciudad) as orden,
        round(o.precio_venta_final,2) as monto,
       (select if(round(sum(importe),2) is null, 0, round(sum(importe),2)) from pagosturismo where id_orden_turismo = o.id) as pagos,
       round(o.precio_venta_final- (select if(round(sum(importe),2) is null, 0, round(sum(importe),2)) from pagosturismo where id_orden_turismo = o.id),2) as saldo
FROM ordenes ord
inner join ordenes_turismo o on ord.id = o.id_orden
inner join ciudades ori on ori.id = ord.id_ciudad_origen and ori.id_estructura = ord.id_estructura_ciudad_origen
inner join ciudades des on des.id = ord.id_ciudad_destino and des.id_estructura = ord.id_estructura_ciudad_destino
where ord.id_cliente = $_POST[cli] and id_estructura_cliente = $_POST[str]";
     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="tablita" name="tablita" class="ui-widget ui-widget-content">
                    <thead>';

     $tabla.='<tr class="ui-widget-header">
                        <th id="interno">Fecha Servicio</th>
                        <th>Origen-Destino</th>
                        <th>Precio Final</th>
                        <th>Pagos A Cuenta</th>
                        <th>Saldo</th>
                        <th>Pago Actual</th>
                        <th>Accion</th>
              </tr>
              </thead><tbody>';
     while ($data = mysql_fetch_array($result)){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='center'>$data[1]</td>
                              <td align='left'>$data[2]</td>
                              <td align='right'>$data[3]</td>
                              <td align='right'>$data[4]</td>
                              <td align='right'>$data[5]</td>
                              <td align='right'><input id='pago$data[0]' align='right' type='text' size='20'></td>";
                 if ($data[4] < $data[3])
                              $tabla.="<td align='right'><input type='button' value='Guardar Pago' id='$data[0]'></td>";
                 else
                              $tabla.="<td></td>";
                 $tabla.="</tr>";
                 $i++;
     }
     $tabla.='</tbody>
              </table>
              <input type="hidden" name="cliente" id="cliente" value="'.$_POST['cli'].'">
              <style type="text/css">
                         #tablita { font-size: 85%; }
                         #tablita tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">
                          $("#tablita input:button").click(function(){
                                                                      var ord = $(this).attr("id");
                                                                      var mont = $("#pago"+ord).val();
                                                                      var cliente = $("#cliente").val();
                                                                      $.post("/modelo/turismo/srvlst.php",{accion:\'loadpago\', cli:cliente, monto:mont, ortur:ord}, function(data){});

                                                                      });
                  </script>';
    print $tabla;
  }
  elseif($accion == 'loadpago'){
        $campos="id, id_cliente, id_estructura_cliente, descripcion, importe, id_user, fecha_alta, id_orden_turismo";
        $values = "$_POST[cli], $_SESSION[structure], 'Pago a cuenta', $_POST[monto], $_SESSION[userid], now(), $_POST[ortur]";
        insert('pagosturismo', $campos, $values);
  }
  elseif($accion == 'rcc'){
       $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
     $cliente = $_POST['cliente'];
         $sql ="select date_format(fservicio, '%d/%m/%Y') as fecha, upper(detalle), round(debe,2), round(haber,2), round(@imonto:= @imonto + (haber-debe),2) as saldo
from (SELECT fservicio, cast(concat(ori.ciudad, ' - ', des.ciudad) as char) as detalle, round(importe,2) as debe, 0 as haber
FROM (select * from ctacteturismo where id_cliente = $_POST[cli]) c
inner join ordenes_turismo o on o.id = c.id_orden
inner join ordenes ord on ord.id = o.id_orden and o.id_estructura_orden = ord.id_estructura
inner join ciudades ori on ori.id = ord.id_ciudad_origen and ori.id_estructura = ord.id_estructura_ciudad_origen
inner join ciudades des on des.id = ord.id_ciudad_destino and des.id_estructura = ord.id_estructura_ciudad_destino
where fservicio between '$desde' and '$hasta'
union all
SELECT date(fecha_alta), descripcion as detalle, 0 as debe, round(importe, 2) as haber
FROM (select * from pagosturismo where id_cliente = $_POST[cli] and date(fecha_alta) between '$desde' and '$hasta') o
union all
select null as fservicio, 'Saldo Anterior' as detalle, (select if(sum(importe) is null, 0, round(sum(importe),2))
       from ctacteturismo c
       inner join ordenes_turismo o on o.id = c.id_orden
       inner join ordenes ord on ord.id = o.id_orden and o.id_estructura_orden = ord.id_estructura
       where ord.id_cliente = $_POST[cli] and fservicio < '$desde') as debe, (select if(sum(importe) is null, 0, round(sum(importe),2)) from pagosturismo where id_cliente = $_POST[cli] and date(fecha_alta) < '$desde') as haber) o, (SELECT @imonto:=0) AS tmp2
order by fservicio";

     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <thead>';

     $data = mysql_fetch_array($result);
           $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Fecha</th>
                        <th>Detalle</th>
                        <th id="fservicio">Debe</th>
                        <th id="nombre">Haber</th>
                        <th id="hsalida">Saldo</th>
                    </tr>
                    </thead>
                    <tbody>';
           while ($data){
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='center'>$data[0]</td>
                              <td align='left'>".utf8_decode($data[1])."</td>
                              <td align='right'>$data[2]</td>
                              <td align='right'>$data[3]</td>
                              <td align='right'>$data[4]</td>
                          </tr>";
                 $data = mysql_fetch_array($result);
                 $i++;
           }
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
              </style>';
    print $tabla;
  }
  
?>

