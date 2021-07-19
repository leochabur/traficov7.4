<?
     set_time_limit(0);
     error_reporting(0);
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
     $cliente=(($_POST[cli])? "and cli.id = $_POST[cli]": "");
   //  die("cli".$_POST[clientes]);
     /*$sql_km = "select sum(km) as km
                from ordenes o
                where (fservicio between '$desde' and '$hasta') $struct $cliente";
     */

     $sql = "(select if(interno is null, 0, interno) as interno, upper(razon_social) as razon_social, count(*) as cant_servicios, round(sum(if(interno is not null, precio,0)),2) as monto, count(pts.id) as cant_precios_cargados,
                     count(u.id) as cant_coches_asignados, facturaPorTramo, sum(o.km) as km_rec,
                     sum((select if(pts.precio is null, max(ptss.precio), pts.precio) as monto from precioTramoServicio ptss WHERE id_cronograma = c.id and id_estructuraCronograma = c.id_estructura)) as alternativo

              from ordenes o
              inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
              inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
              inner join clientes cli on cli.id = c.id_cliente and cli.id_estructura = c.id_estructura_cliente
              inner join tipofacturacioncliente tfc on tfc.id_cliente = cli.id and tfc.id_estructuracliente = cli.id_estructura
              left join unidades u on u.id = o.id_micro
              left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura =  u.id_estructura_tipounidad
              left join precioTramoServicio pts on pts.id_cronograma = c.id and  pts.id_estructuraCronograma = c.id_estructura and pts.id_tipoUnidad = tu.id and pts.id_estructuraTipoUnidad = tu.id_estructura
              where o.fservicio between '$desde' and '$hasta' and (o.id_estructura = $_POST[str]) and (not o.borrada) and (not o.suspendida) and facturaPorTramo $cliente
              group by cli.razon_social,u.interno
              order by razon_social, interno)
              union all
              (select interno, upper(razon_social),
                      count(*),
                      0 as km,
                      count(*),
                      count(u.id) as cant_coches_asignados, facturaPorTramo, sum(o.km) as km_rec,
                      round(sum(o.km)*(montoMensualFacturacion/(select sum(km) from ordenes where id_cliente = cli.id and id_estructura_cliente = cli.id_estructura and not borrada and not suspendida and fservicio between '$desde' and '$hasta')),2) as alternativo
              from ordenes o
              inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
              inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
              inner join clientes cli on cli.id = c.id_cliente and cli.id_estructura = c.id_estructura_cliente
              inner join tipofacturacioncliente tfc on tfc.id_cliente = cli.id and tfc.id_estructuracliente = cli.id_estructura
              left join unidades u on u.id = o.id_micro
              where o.fservicio between '$desde' and '$hasta' and (o.id_estructura = $_POST[str]) and (not o.borrada) and (not o.suspendida)and not facturaPorTramo $cliente
              group by cli.razon_social, u.interno)
              order by razon_social, interno";
  //   die($sql);
     $conn = conexcion();
/*     $res_km = mysql_query($sql_km, $conn);
     if ($row = mysql_fetch_array($res_km)){
        $km = $row['km'];
     }    */

     $result = mysql_query($sql, $conn) or die(mysql_error($conn));

     //<a href="/modelo/informes/trafico/exportkmcli.php?des='.$desde.'&has='.$hasta.'&cli='.$cliente.'&str='.$struct.'"><img title="Exportar a Excel" src="../../../vista/excel.jpg" width="35" height="35" border="0"></a>
     $tabla='
             <table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $total_serv=0;
     $total_reca=0;
     $total_km=0;

     while ($data){
           $cliente = $data[1];

           $i = 0;
           $razon_social = ($data[1]?"CLIENTE $data[1]": "");
           $tabla.='<tr class="ui-widget-header">
                        <th colspan="6">'.$razon_social.'</th>
                    </tr>
                    <tr>
                        <th id="razon_social">Interno</th>
                        <th id="interno">Cantidad Servicios</th>
                        <th id="fservicio">Recaudacion</th>
                        <th id="fservicio">KM</th>
                        <th id="fservicio">$/KM</th>
                        <th id="nombre">Observaciones</th>
                    </tr>';
           $parcial_serv=0;
           $parcial_reca=0;
           $parcial_km=0;

           while (($data)&&($cliente == $data[1])){
                 $obs="";
                 if ($data[2] != $data[4]){
                   $obs="Existen ".($data[2]-$data[4])." servicios sin importes!";
                 }
                 if ($data[2] != $data[5]){
                   $obs.=" - Existen ".($data[2]-$data[5])." servicios sin coche!";
                 }
                                      // die("okaaaaaaaaa vdvdvvdvvd dentroooo dentrorrororororo   $cliente   $razon_social");
                 $parcial_serv+=$data[2];
                 $parcial_reca+=$data[alternativo];
                 $parcial_km+=$data[km_rec];
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>$data[0]</td>
                              <td align='right'>$data[2]</td>
                              <td align='right'>$ ".number_format($data[alternativo],2)."</td>
                              <td align='right'>$data[km_rec]</td>
                              <td align='right'>$ ".number_format(($data[alternativo]/$data[km_rec]),2)."</td>
                              <td align='left'>$obs</td>
                          </tr>";
                 $data = mysql_fetch_array($result);
                 $i++;
           }
           $total_serv+=$parcial_serv;
           $total_reca+=$parcial_reca;
           $total_km+=$parcial_km;
           $tabla.="<tr>
                        <tr>
                            <td><b>TOTAL CLIENTE $cliente</b></td>
                            <td align='right'><b>$parcial_serv</b></td>
                            <td align='right'><b>$ ".number_format($parcial_reca,2,'.',',')."</b></td>
                            <td align='right'><b>".number_format($parcial_km,2)."</b></td>
                            <td align='right'><b>$ ".number_format(($parcial_reca/$parcial_km),2)."</b></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan='6'><hr align='tr'></td>
                        </tr>";
     }
     $tabla.='<tr>
                  <td colspan="6"><hr align="tr"></td>
              </tr>
              <tr>
                            <td><b>TOTALES EN EL PERIODO</b></td>
                            <td align="right"><b>'.$total_serv.'</b></td>
                            <td align="right"><b>$ '.number_format($total_reca,2,'.',',').'</b></td>
                            <td align="right"><b>'.number_format($total_km,2,'.',',').'</b></td>
                            <td align="right"><b>$ '.number_format($total_reca/$total_km,2,'.',',').'</b></td>
                            <td></td>
              </tr>
              </tbody>
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

