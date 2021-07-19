<?php
     set_time_limit(0);
error_reporting(E_ALL & ~E_NOTICE);
  session_start();
  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
    include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
  include ('../../vista/paneles/viewpanel.php');
  include('../../modelsORM/manager.php');
  include_once('../../modelsORM/call.php');  
  include_once('../../modelsORM/controller.php');  
  include('../../modelsORM/src/ArticuloCliente.php');
    
$accion = $_POST['accion'];
if ($accion == 'load'){
   if ($_POST[tipof] == 't'){
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
       $cronos = "(select c.id, upper(c.nombre) as nombre, precio_unitario, c.id_estructura, c.id_cliente, ciudades_id_origen, ciudades_id_destino, upper(o.nombre) as orden
                  from ordenes o
                  inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
                  inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                  where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and not borrada and not suspendida and o.id_cliente = $_POST[clientes]
                  group by s.id_cronograma) ";
       $sql = "select c.id, concat(upper(nombre), '  (', orden,')'), upper(o.ciudad), upper(d.ciudad), precio_unitario
               from $cronos c
               inner join (select id as id_o, ciudad from ciudades where id_estructura = $_POST[str]) o on o.id_o = c.ciudades_id_origen
               inner join (select id as id_d, ciudad from ciudades where id_estructura = $_POST[str]) d on d.id_d = c.ciudades_id_destino
               where id_estructura = $_POST[str] and id_cliente = $_POST[clientes]
               order by nombre";
    //   die($sql);
       $select = armarSelect ("articulosClientes", "articulo", "id", "articulo", "id_cliente = $_POST[clientes] and id_estructura_cliente = $_POST[str]", 1);
    
       $conn = conexcion();
       $tipos = getTipoCoches($_POST[str], $conn);
       $precios = loadPrecioTramo($conn, $_POST[str], $_POST[clientes]);
       $result = mysql_query($sql, $conn);
       $tabla='<table id="example-advanced" name="example-advanced" border="1" widht="75%">

                    <tbody>';
       while ($data = mysql_fetch_array($result)){
             $tabla.="<tr class='ui-widget-header'>
                          <td>".htmlentities($data[1])."</td>
                          <td>$data[2]</td>
                          <td>$data[3]</td>
                          <td>Articulo</td>
                          <td>Presupuestado</td>
                          <td></td>
                      </tr>";
             foreach ($tipos as $clave => $valor){
                     $white="";

                     $key = "$data[0]$_POST[str]$clave$_POST[str]";
                     if ($precios[$key][4]){
                        $white="<option value='0'></option>";
                     }
                     $tabla.="<tr>
                                  <td colspan='2' align='right'>".str_pad($valor, 20,'_')."</td>
                                  <td><input type='text' size='7' id='data-$data[0]-$clave' value='".$precios[$key][1]."'></td>
                                  <td><select id='art-$data[0]-$clave'>
                                              <option value='".$precios[$key][4]."'>".$precios[$key][5]."</option>
                                              $white
                                              $select
                                      </select></td>
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
                                                                                                             var articulo = $('#art-'+id).val();
                                                                                                             if ($.isNumeric(value)){
                                                                                                                $.post('/modelo/servicios/valcro.php', {id_cron: res[0], id_tipo: res[1], price: value, accion:'updvalue', cli: $_POST[clientes], str: $_POST[str], pr: pres, art:articulo}, function(data){\$('#td-'+id).empty();});
                                                                                                             }
                                                                                                             else{
                                                                                                                  alert('El valor ingresado es invalido!');
                                                                                                                  $('#data-'+id).select();
                                                                                                             }
                                                                                                         });</script>";
       mysql_close($conn);
       print $tabla;
   }
   elseif($_POST[tipof] == 'f'){
        $conn = conexcion();
        $sql = "SELECT montoMensualFacturacion
                FROM tipofacturacioncliente
                where id_cliente = $_POST[clientes] and id_estructuracliente = $_POST[str]";
      //  die($sql);
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
                                                                    $.post("/modelo/servicios/valcro.php", {tipoF:$("#tipof").val(), monto: value, accion:"updtpo", cli: '.$_POST[clientes].', str: '.$_POST[str].'}, function(data){$("#tdsave").empty();});
                                                                 }
                                                                 else{
                                                                      alert("El valor ingresado es invalido!");
                                                                 }
                                                                 });
                 </script>';
        print $tabla;
   }
   elseif ($_POST[tipof] == 'a'){
        $conn = conexcion();
        $sql = "SELECT montoMensualFacturacion
                FROM tipofacturacioncliente
                where id_cliente = $_POST[clientes] and id_estructuracliente = $_POST[str]";
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
                                    <th>Importe Mensual Facturacion</th>
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
                 </table>';

   
   
   
   
     $desde = dateToMysql($_POST['desde'], '/');
     $hasta = dateToMysql($_POST['hasta'], '/');
       $cronos = "(select c.id, upper(c.nombre) as nombre, precio_unitario, c.id_estructura, c.id_cliente, ciudades_id_origen, ciudades_id_destino, upper(o.nombre) as orden
                  from ordenes o
                  inner join servicios s on s.id = o.id_servicio and o.id_estructura_servicio = s.id_estructura
                  inner join cronogramas c on c.id = s.id_cronograma and c.id_estructura = s.id_estructura_cronograma
                  where fservicio between '$desde' and '$hasta' and o.id_estructura = $_POST[str] and not borrada and not suspendida and o.id_cliente = $_POST[clientes]
                  group by s.id_cronograma) ";
       $sql = "select c.id, concat(upper(nombre), '  (', orden,')'), upper(o.ciudad), upper(d.ciudad), precio_unitario
               from $cronos c
               inner join (select id as id_o, ciudad from ciudades where id_estructura = $_POST[str]) o on o.id_o = c.ciudades_id_origen
               inner join (select id as id_d, ciudad from ciudades where id_estructura = $_POST[str]) d on d.id_d = c.ciudades_id_destino
               where id_estructura = $_POST[str] and id_cliente = $_POST[clientes]
               order by nombre";
    //   die($sql);
       $tipos = getTipoCoches($_POST[str], $conn);
       $precios = loadPrecioTramo($conn, $_POST[str], $_POST[clientes]);
       $result = mysql_query($sql, $conn);
       $tabla.='<br><table id="example-advanced" name="example-advanced" border="1" widht="75%">

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
                                                                                                                $.post('/modelo/servicios/valcro.php', {id_cron: res[0], id_tipo: res[1], price: value, accion:'updvalue', cli: $_POST[clientes], str: $_POST[str], pr: pres}, function(data){\$('#td-'+id).empty();});
                                                                                                             }
                                                                                                             else{
                                                                                                                  alert('El valor ingresado es invalido!');
                                                                                                                  $('#data-'+id).select();
                                                                                                             }
                                                                                                         });</script>";
       mysql_close($conn);

       

                 $tabla.='<script>
                         $("#tdstr").append($("#str option:selected").text());
                         $("#tdcli").append($("#clientes option:selected").text());
                         $("#savemen").button().click(function(){
                                                                 var value = $("#price").val();
                                                                 if ($.isNumeric(value)){
                                                                    $.post("/modelo/servicios/valcro.php", {tipoF:$("#tipof").val(), monto: value, accion:"updtpo", cli: '.$_POST[clientes].', str: '.$_POST[str].'}, function(data){$("#tdsave").empty();});
                                                                 }
                                                                 else{
                                                                      alert("El valor ingresado es invalido!");
                                                                 }
                                                                 });
                 </script>';
                        print $tabla;
   ///
   }
}
elseif($accion=='updvalue'){
    $conn=conexcion();
    $art = ($_POST['art']?$_POST['art']:'NULL');
    $sql = "INSERT INTO precioTramoServicio (id_cronograma, id_estructuraCronograma, id_tipoUnidad, id_estructuraTipoUnidad, precio, id_cliente, id_estructuraCliente, presupuestado, id_articulo)
            VALUES ($_POST[id_cron], $_POST[str], $_POST[id_tipo], $_POST[str], $_POST[price], $_POST[cli], $_POST[str], $_POST[pr], $art)
            ON DUPLICATE KEY UPDATE precio = $_POST[price], presupuestado = $_POST[pr], id_articulo = $art";
    mysql_query($sql, $conn);
    $ok= mysql_errno($conn);
    mysql_close($conn);
    print $ok;
    
}
elseif($accion=='updtpo'){
    $conn=conexcion();

    $sql = "INSERT INTO tipofacturacioncliente (id_cliente, id_estructuracliente, tipoFacturacion)
            VALUES ($_POST[clientes], $_POST[str], '$_POST[tipof]')
            ON DUPLICATE KEY UPDATE tipoFacturacion = '$_POST[tipof]'";
    if (isset($_POST[monto])){
           $sql = "INSERT INTO tipofacturacioncliente (id_cliente, id_estructuracliente, tipoFacturacion, montoMensualFacturacion)
                   VALUES ($_POST[cli], $_POST[str], '$_POST[tipof]', $_POST[monto])
                   ON DUPLICATE KEY UPDATE tipoFacturacion = '$_POST[tipof]', montoMensualFacturacion = $_POST[monto]";
    }

 /*   }
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
    }    */
    mysql_query($sql, $conn);
    $ok= mysql_errno($conn);
    mysql_close($conn);
    print $sql;

}
elseif($accion =='addart'){
    try{
      $cli = getCliente($_POST['clientes'], $_SESSION['structure']);
      if (!$_POST['desc']){
            print json_encode(array('ok' => false, 'message'=>'El campo codigo no puede permanecer en blanco!! '));
            exit();
        }
      $art = new ArticuloCliente();
      $art->setCliente($cli);
      $art->setDescripcion($_POST[desc]);
      $response = array('ok' => true, 'message'=>'');
      $entityManager->persist($art);
      $entityManager->flush();
      print json_encode($response);
    } catch (Exception $e) {
      print json_encode(array('ok' => false, 'price'=>"$price", 'message'=>'Se han producido errores al realizar la accion!! '));
  } 
}
elseif($accion == 'setPcie'){
    try{
      $art = find('ArticuloCliente', $_POST['art']);
      $art->setImporte($_POST['pcie']);
      $response = array('ok' => true, 'message'=>'');
      $entityManager->flush();
      print json_encode($response);
    } catch (Exception $e) {
      print json_encode(array('ok' => false, 'price'=>"$price", 'message'=>'Se han producido errores al realizar la accion!! '));
  }   
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
    $sql = "SELECT concat(id_cronograma, id_estructuraCronograma, id_tipoUnidad, id_estructuraTipoUnidad) as clave, precio, p.id, presupuestado, if(a.id is null, 0, a.id) as id_articulo, upper(if(a.id is null,'',a.articulo)) as articulo
            FROM precioTramoServicio p
            left join articulosClientes a on a.id = p.id_articulo
            where p.id_cliente = $cli and p.id_estructuraCliente = $str";
    $result = mysql_query($sql, $conn);
    $precios = array();
    while ($data = mysql_fetch_array($result)){
          $precios[$data[clave]] = array(0=>$data[id], 1=>$data[precio], 3=>$data[presupuestado], 4=>$data[id_articulo], 5=>$data[articulo]);
    }
    return $precios;
}

  
?>

