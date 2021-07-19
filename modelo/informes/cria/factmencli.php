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
  elseif($accion == 'reskm'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');

   /*  $sql = "Select nombre, count(*) as cant, 'Sin coche asignado' as tipo, null as precio, null as total
              from (select * from ordenes where fservicio between '$desde' and '$hasta' and id_cliente = $_POST[cli] and id_estructura = $_POST[str] and id_micro is null) o
              inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
              group by s.id_cronograma
              union all
              select nombre, cant, tipo, precio, (precio * cant) as total
              from (
              select count(*) as cant, nombre, s.id_cronograma, tu.id as id_tipo, s.id_estructura_cronograma as estrcron, id_cliente, id_estructura_cliente, tu.tipo
              from (select * from ordenes where fservicio between '$desde' and '$hasta' and id_cliente = $_POST[cli] and id_estructura = $_POST[str] and id_micro is not null) o
              inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
              left join unidades u on u.id = o.id_micro
              left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
              group by s.id_cronograma, tu.id) o
              left join precioTramoServicio pts on pts.id_cronograma = o.id_cronograma and pts.id_estructuraCronograma = estrcron and
                                                   id_tipoUnidad = o.id_tipo and pts.id_cliente = o.id_cliente and id_estructuraCliente = id_estructura_cliente";     */

    $sql="select upper(ciudad), tipo, ac.articulo, count(*), precio, round((count(*)*precio),2), ac.id as id_art
from (
select o.nombre, if (id_micro is null or u.id_tipoUnidad is null, 1,0) as anomalia, tu.tipo,
       if (id_articulo is not null, id_articulo, (SELECT id_articulo
                                 FROM precioTramoServicio p
                                 where id_cronograma = c.id and id_estructuraCronograma = c.id_estructura and (id_articulo or presupuestado) limit 1)) as articulo,
       if (id_articulo is not null, precio, (SELECT precio
                                 FROM precioTramoServicio p
                                 where id_cronograma = c.id and id_estructuraCronograma = c.id_estructura and (id_articulo or presupuestado) limit 1)) as precio,
       if (s.i_v = 'i', ori.ciudad, d.ciudad) as ciudad

from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
left join ciudades ori on ori.id = ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen
left join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
left join unidades u on u.id = o.id_micro
left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipoUnidad
left join precioTramoServicio pts on pts.id_cronograma = c.id and pts.id_estructuraCronograma = c.id_estructura and pts.id_tipoUnidad = tu.id and pts.id_estructuraTipoUnidad = tu.id_estructura
where fservicio between '$desde' and '$hasta'  and o.id_estructura = $_POST[str] and o.id_cliente = $_POST[cli] and not borrada and not suspendida) o
left join articulosClientes ac on ac.id = o.articulo
group by o.articulo
order by ac.orden";
  //   die($sql);




     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Localidad</th>
                        <th id="razon_social">Tipo</th>
                        <th id="interno">Articulo</th>
                        <th id="fservicio">Cantidad</th>
                        <th id="nombre">Precio Unitario</th>
                        <th id="nombre">Total</th>
                    </tr>';
     $totaf=0;
     while ($data){
                 if ($data[articulo]){
                    $id_art="$data[id_art]";
                 }
                 else{
                      $id_art='ND';
                 }
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $total = $data[cantidad]*$data[unitario];
                 $tabla.="<tr bgcolor='$color' id='$id_art'>
                              <td align='left'>".htmlentities($data[0])."</td>
                              <td align='left'>$data[1]</td>
                              <td align='left'>$data[2]</td>
                              <td align='right'>$data[3]</td>
                              <td align='right'>$$data[4]</td>
                              <td align='right'>$$data[5]</td>
                          </tr>";
                 $totaf+= $data[5];
                 $data = mysql_fetch_array($result);
                 $i++;
     }
     $tabla.='<tr><td colspan="5" align="right">Importe Total</td><td align="right">$ '.number_format($totaf,2).'</td></tr></tbody>
              </table>
              <style type="text/css">
                         #example { font-size: 85%; }
                         #example tbody tr:hover {background-color: #FF8080;}
                  </style>
                  <script type="text/javascript">
                          $("#example tr").click(function(){
                                                            var art = $(this).attr("id");
                                                            var dialog = $(\'<div style="display:none" id="dialog" class="loading" align="center"></div>\').appendTo("body");
                                                                    dialog.dialog({
                                                                                   close: function(event, ui) {dialog.remove();},
                                                                                   title: "Detalle servicios realizados",
                                                                                   width:1100,
                                                                                   height:450,
                                                                                   modal:true,
                                                                                         show: {
                                                                                                effect: "blind",
                                                                                                duration: 350
                                                                                         },
                                                                                         hide: {
                                                                                               effect: "blind",
                                                                                               duration: 350
                                                                                               }
                                                                                   });
                                                                                   dialog.load("/modelo/informes/cria/factmencli.php",{articulo: art, accion:"deta", des:"'.$desde.'", has:"'.$hasta.'", cli:'.$_POST[cli].', str:'.$_POST[str].'},function (responseText, textStatus, XMLHttpRequest) {dialog.removeClass("loading");});
                                                            
                                                            
                                                            });
                  </script>
';
    print $tabla;
  }
  elseif($accion == 'deta'){
       $articulo = "o.articulo = $_POST[articulo]";
       if ($_POST[articulo] == 'ND'){
          $articulo = "o.articulo is null";
       }
                 $sql = "select date_format(fservicio,'%d/%m/%Y'), nombre, ori, des, interno, tipo, ac.articulo,  precio
from (
select fservicio, o.nombre, if (id_micro is null or u.id_tipoUnidad is null, 1,0) as anomalia, tu.tipo,
       if (id_articulo is not null, id_articulo, (SELECT id_articulo
                                 FROM precioTramoServicio p
                                 where id_cronograma = c.id and id_estructuraCronograma = c.id_estructura and (id_articulo or presupuestado) limit 1)) as articulo,
       if (id_articulo is not null, precio, (SELECT precio
                                 FROM precioTramoServicio p
                                 where id_cronograma = c.id and id_estructuraCronograma = c.id_estructura and (id_articulo or presupuestado) limit 1)) as precio,
       ori.ciudad as ori, d.ciudad as des, interno, o.id_cliente, o.id_estructura

from ordenes o
inner join servicios s on s.id = o.id_servicio and s.id_estructura = o.id_estructura_servicio
inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
left join ciudades ori on ori.id = ciudades_id_origen and ori.id_estructura = c.ciudades_id_estructura_origen
left join ciudades d on d.id = ciudades_id_destino and d.id_estructura = ciudades_id_estructura_destino
left join unidades u on u.id = o.id_micro
left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipoUnidad
left join precioTramoServicio pts on pts.id_cronograma = c.id and pts.id_estructuraCronograma = c.id_estructura and pts.id_tipoUnidad = tu.id and pts.id_estructuraTipoUnidad = tu.id_estructura
where fservicio between '$_POST[des]' and '$_POST[has]' and not suspendida and not borrada and o.id_cliente = $_POST[cli] and o.id_estructura = $_POST[str]) o
left join articulosClientes ac on ac.id = o.articulo
where $articulo
                         order by fservicio";
   // die($sql);

     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="deta" name="deta" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $tabla.='<tr class="ui-widget-header">
                        <th>Orden</th>
                        <th id="razon_social">Fecha</th>
                        <th id="razon_social">Servicio</th>
                        <th id="interno">Origen</th>
                        <th id="fservicio">Destino</th>
                        <th id="nombre">Interno</th>
                        <th id="nombre">Tipo Unidad</th>
                    </tr>';
     $totaf=0;
     $i=0;
     $j=1;
     while ($data){
                 if ($data[articulo]){
                    $id_art="$data[id_art]";
                 }
                 else{
                      $id_art='ND';
                 }
                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $total = $data[cantidad]*$data[unitario];
                 $tabla.="<tr bgcolor='$color' id='$id_art'>
                              <td>".$j++."</td>
                              <td align='left'>".htmlentities($data[0])."</td>
                              <td align='left'>$data[1]</td>
                              <td align='left'>$data[2]</td>
                              <td align='left'>$data[3]</td>
                              <td align='right'>$data[4]</td>
                              <td align='left'>$data[5]</td>
                          </tr>";
                 $totaf+= $data[5];
                 $data = mysql_fetch_array($result);
                 $i++;
     }
     mysql_close($conn);
     $tabla.='</tbody>
              </table>
              <style type="text/css">
                         #deta { font-size: 65%; }
                         #deta tbody tr:hover {background-color: #FF8080;}
                  </style>';
     print $tabla;
  }
  
?>

