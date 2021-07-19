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

    $sql="select upper(o.nombre) as servicio, upper(tipo) as tipo, count(*) as cantidad, interno,
                 sum(if(o.id_micro is null,0,1)) as cant_unid_asig, sum(if(precio is null,0,1)) as can_imp,
                 round(precio,2) as unitario, sum(if(u.id_tipounidad is null,0,1)) as cant_tipos
          from ordenes o
          inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
          inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
          left join unidades u on u.id = o.id_micro
          left join tipounidad tu on tu.id = u.id_tipounidad and tu.id_estructura = u.id_estructura_tipounidad
          left join precioTramoServicio ptm on ptm.id_cronograma = s.id_cronograma and ptm.id_estructuraCronograma = s.id_estructura_cronograma
                                               and u.id_tipounidad = ptm.id_tipoUnidad and u.id_estructura_tipounidad = ptm.id_estructuraTipoUnidad
          where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and not borrada and not suspendida and o.id_cliente = $_POST[cli]
          group by o.id_servicio, tu.id
          order by servicio, tipo";
  //   die($sql);




     $conn = conexcion();

     $result = mysql_query($sql, $conn);
     $tabla='<table width="100%" id="example" name="example" class="ui-widget ui-widget-content">
                    <tbody>';

     $data = mysql_fetch_array($result);
     $tabla.='<tr class="ui-widget-header">
                        <th id="razon_social">Servicio</th>
                        <th id="razon_social">Tipo Unidad</th>
                        <th id="interno">Cant. Serv.</th>
                        <th id="fservicio">Precio Unitario</th>
                        <th id="nombre">Total</th>
                        <th id="nombre">Observaciones</th>
                    </tr>';
     $totaf=0;
     while ($data){
                 $sch = (($data[cant_unid_asig] < $data[cantidad])?'Existen servicios sin unidad asignada!':0);
                 $tipo = (($data[cant_tipos] < $data[cantidad])?'Existen unidades sin tipo!':0);
                 $price = (($data[can_imp] < $data[cantidad])?'Existen servicios sin importe!':0);
                 $obs="";
                 if (!$sch){
                    if (!$tipo){
                       if ($price){
                          $obs = "No existe valor cargado para el servicio!";
                       }
                    }
                    else{
                         $obs="No se ha cargado un tipo de unidad!";
                    }
                 }
                 else{
                      $obs = "No existe coche asignado al servicio!";
                 }

                 $color = (($i%2)==0) ? "#D3D3D3" : "#F3F3F3";
                 $total = $data[cantidad]*$data[unitario];
                 $tabla.="<tr bgcolor='$color'>
                              <td align='left'>".htmlentities($data[0])."</td>
                              <td align='left'>$data[tipo]</td>
                              <td align='right'>$data[cantidad]</td>
                              <td align='right'>$$data[unitario]</td>
                              <td align='right'>$$total</td>
                              <td align='left'>$obs</td>
                          </tr>";
                 $totaf+= $total;
                 $data = mysql_fetch_array($result);
                 $i++;
     }
     $tabla.='<tr><td colspan="3">Importe Total</td><td align="right">$ '.number_format($totaf,2).'</td></tr></tbody>
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

