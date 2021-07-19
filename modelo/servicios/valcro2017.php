<?
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
    include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
    
$accion = $_POST['accion'];
if ($accion == 'load'){
   if ($_POST[fctr]){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
       $cronos = "(select c.id, upper(c.nombre) as nombre, precio_unitario, c.id_estructura, c.id_cliente, ciudades_id_origen, ciudades_id_destino, upper(o.nombre) as orden
                  from ordenes o
                  inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
                  inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                  where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and not borrada and not suspendida and o.id_cliente = $_POST[cli]
                  group by s.id_cronograma) ";
      /* if (($_POST['last'])){
          $cronos = "(select c.id, c.id_cliente, c.id_estructura, c.nombre, c.ciudades_id_origen, c.ciudades_id_destino, c.activo, precio_unitario
                     from ordenes o
                     inner join (select * from servicios where id_estructura = $_POST[str]) s on s.id = o.id_servicio
                     inner join (select * from cronogramas where id_estructura = $_POST[str]) c on c.id = s.id_cronograma
                     where fservicio between date_sub(date(now()), interval 20 day) and date(now()) and o.id_estructura = $_POST[str]
                     group by c.id)";
       }   */

       $sql = "select c.id, concat(upper(nombre), '  (', orden,')'), upper(o.ciudad), upper(d.ciudad), precio_unitario
               from $cronos c
               inner join (select id as id_o, ciudad from ciudades where id_estructura = $_POST[str]) o on o.id_o = c.ciudades_id_origen
               inner join (select id as id_d, ciudad from ciudades where id_estructura = $_POST[str]) d on d.id_d = c.ciudades_id_destino
               where id_estructura = $_POST[str] and id_cliente = $_POST[cli]
               order by nombre";
    //   die($sql);
       $conn = conexcion();
       $tipos = getTipoCoches($_POST[str], $conn);
       $precios = loadPrecioTramo($conn, $_POST[str], $_POST[cli]);
       $result = mysql_query($sql, $conn);
       $tabla='<table id="example-advanced" name="example-advanced" border="1" widht="75%">

                    <tbody>';
       while ($data = mysql_fetch_array($result)){
             $tabla.="<tr class='ui-widget-header'>
                          <td>".htmlentities($data[1])."</td>
                          <td>$data[2]</td>
                          <td>$data[3]</td>
                          <td>Presupuestado</td>
                          <td></td>
                      </tr>";
             foreach ($tipos as $clave => $valor){
                     $key = "$data[0]$_POST[str]$clave$_POST[str]";
               //      die($key);
                     $tabla.="<tr>
                                  <td colspan='2' align='right'>".str_pad($valor, 20,'_')."</td>
                                  <td><input type='text' size='7' id='data-$data[0]-$clave' value='".$precios[$key][1]."'></td>
                                  <td align='right'><input type='checkbox' ".($precios[$key][3]?'checked':'')." id='pres-$data[0]-$clave'></td>
                                  <td id='td-$data[0]-$clave'><input type='button' value='Guardar' id='$data[0]-$clave'></td>
                             </tr>";
             }
       }
       $tabla.="</tbody></thead><script>
                                        $('#example-advanced').treetable();
                                        $('#example-advanced tr input:button').button().click(function(){
                                                                                                             var id = $(this).attr('id');
                                                                                                             var res = id.split('-');
                                                                                                             var value = $('#data-'+id).val();
                                                                                                             var pres = 0;
                                                                                                             if($('#pres-'+id).is(':checked')) {
                                                                                                                pres = 1;
                                                                                                             }
                                                                                                             if ($.isNumeric(value)){
                                                                                                                $.post('/modelo/servicios/valcro.php', {id_cron: res[0], id_tipo: res[1], price: value, accion:'updvalue', cli: $_POST[cli], str: $_POST[str], pr: pres}, function(data){\$('#td-'+id).empty();});
                                                                                                             }
                                                                                                             else{
                                                                                                                  alert('El valor ingresado es invalido!');
                                                                                                                  $('#data-'+id).select();
                                                                                                             }
                                                                                                         });</script>";
       mysql_close($conn);
       print $tabla;
   }
   else{
        $conn = conexcion();
        $sql = "SELECT montoMensualFacturacion
                FROM tipofacturacioncliente
                where id_cliente = $_POST[cli] and id_estructuracliente = $_POST[str]";
        $result = mysql_query($sql, $conn);
        $value=0;
        if ($data = mysql_fetch_array($result)){
           $value=$data[0];
        }
        $tabla = '<br><table border="1" width="100%">
                         <thead>
                                <tr class="ui-widget-header">
                                    <th>Estructura</th>
                                    <th>Cliente</th>
                                    <th>Importe Facturacion</th>
                                    <th>Accion</th>
                                </tr>
                         </thead>
                         <tbody>
                                <tr>
                                  <td id="tdstr"></td>
                                  <td id="tdcli"></td>
                                  <td align="right"><input type="text" size="20" class="ui-corner-all" id="price" value="'.$value.'" align="right"></td>
                                  <td align="center" id="tdsave"><input type="button" value="Guardar/Modificar" id="savemen"></td>
                                </tr>
                         </tbody>
                 </table>
                 <script>
                         $("#tdstr").append($("#str option:selected").text());
                         $("#tdcli").append($("#clientes option:selected").text());
                         $("#savemen").button().click(function(){
                                                                 var value = $("#price").val();
                                                                 if ($.isNumeric(value)){
                                                                    $.post("/modelo/servicios/valcro.php", {ftramo:0, monto: value, accion:"updtpo", cli: '.$_POST[cli].', str: '.$_POST[str].'}, function(data){$("#tdsave").empty();});
                                                                 }
                                                                 else{
                                                                      alert("El valor ingresado es invalido!");
                                                                 }
                                                                 });
                 </script>';
        print $tabla;
   }
}
elseif($accion=='updvalue'){
    $conn=conexcion();
    $sql = "INSERT INTO precioTramoServicio (id_cronograma, id_estructuraCronograma, id_tipoUnidad, id_estructuraTipoUnidad, precio, id_cliente, id_estructuraCliente, presupuestado)
            VALUES ($_POST[id_cron], $_POST[str], $_POST[id_tipo], $_POST[str], $_POST[price], $_POST[cli], $_POST[str], $_POST[pr])
            ON DUPLICATE KEY UPDATE precio = $_POST[price], presupuestado = $_POST[pr]";
    mysql_query($sql, $conn);
    $ok= mysql_errno($conn);
    mysql_close($conn);
    print $ok;
    
}
elseif($accion=='updtpo'){
    $conn=conexcion();
    if ($_POST[ftramo]){
           $sql = "INSERT INTO tipofacturacioncliente (id_cliente, id_estructuracliente, facturaPorTramo)
                   VALUES ($_POST[cli], $_POST[str], $_POST[ftramo])
                   ON DUPLICATE KEY UPDATE facturaPorTramo = $_POST[ftramo]";
    }
    else{
         if (isset($_POST[monto])){
            $campoMonto = ", montoMensualFacturacion";
            $monto = ", $_POST[monto]";
            $updMonto = ", montoMensualFacturacion = $_POST[monto]";
         }
         else{
            $campoMonto = "";
            $monto = "";
            $updMonto = "";
         }
           $sql = "INSERT INTO tipofacturacioncliente (id_cliente, id_estructuracliente, facturaPorTramo $campoMonto)
                   VALUES ($_POST[cli], $_POST[str], $_POST[ftramo] $monto)
                   ON DUPLICATE KEY UPDATE facturaPorTramo = $_POST[ftramo] $updMonto";
    }
    mysql_query($sql, $conn);
    $ok= mysql_errno($conn);
    mysql_close($conn);
    print $sql;

}

function getTipoCoches($estr, $conn){
    $sql = "SELECT id, upper(tipo) as tipo FROM tipounidad  where id_estructura = $estr and id <> 8 order by tipo";
    $result = mysql_query($sql, $conn);
    $tipos = array();
    while ($data = mysql_fetch_array($result)){
          $tipos[$data[id]] = $data[tipo];
    }
    return $tipos;
}

function loadPrecioTramo($conn, $str, $cli){
    $sql = "SELECT concat(id_cronograma, id_estructuraCronograma, id_tipoUnidad, id_estructuraTipoUnidad) as clave, precio, id, presupuestado
            FROM precioTramoServicio
            where id_cliente = $cli and id_estructuraCliente = $str";
    $result = mysql_query($sql, $conn);
    $precios = array();
    while ($data = mysql_fetch_array($result)){
          $precios[$data[clave]] = array(0=>$data[id], 1=>$data[precio], 3=>$data[presupuestado]);
    }
    return $precios;
}

  
?>

