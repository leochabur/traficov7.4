<?
  session_start();

  if (!$_SESSION['auth']){
     print "<meta http-equiv=Refresh content=\"0 ; url=/\">";
     exit;
  }
  include ('../../controlador/bdadmin.php');
  include ('../../modelo/utils/dateutils.php');
  include_once('../../vista/paneles/viewpanel.php');
  include('../../controlador/ejecutar_sql.php');

  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'list'){
     $conn = conexcion();
     $cli = $_POST['cliente'];
     $fecha = $_POST['fecha'];
     $hasta = $_POST['hasta'];
     if ($cli != 0)
        $cli = "and c.id = $cli";
     else
         $cli = "";


    $sql="SELECT date_format(fservicio, '%d/%m/%Y') as fecha, nombre as servicio, hsalida, precio_venta_final, o.id
          FROM ordenes o
          inner join ordenes_turismo ot on ot.id_orden = o.id
          inner join clientes c on (c.id = o.id_cliente) and (c.id_estructura = o.id_estructura_cliente)
          WHERE ((fservicio between '$fecha' and '$hasta') and (o.id_estructura = $_SESSION[structure])) and (not suspendida) and (not borrada) and afecta_ctacte $cli
                and o.id not in (select id_orden from ordenesxfactura)";
    //  die($sql);
     $result = mysql_query($sql, $conn);

     $data = mysql_fetch_array($result);
     $tabla='<fieldset class="ui-widget ui-widget-content ui-corner-all">
             <legend class="ui-widget ui-widget-header ui-corner-all">Factura de Cliente</legend>
             <form id="addfact">
             <table width="75%" align="center">
                    <tr>
                        <td>Fecha Factura </td>
                        <td><input type="text" size="20" id="fechaf" class="required" name="fechaf"></td>
                        <td>Tipo Comprobante </td>
                        <td><select id="tipoc" name="tipoc">'.armarSelect('tipo_comprobantes', 'tipo', 'id', 'tipo', "",1).'</select></td>
                        <td>Nro Orden Compra</td>
                        <td><input type="text" size="8" id="orco" name="orco"></td>
                    </tr>
                    <tr>
                        <td>Tipo Factura </td>
                        <td><select id="tipof" name="tipof"><option value="A">A<option value="B">B</option><option value="X">X</option></option></select></td>
                        <td>Punto Venta</td>
                        <td><input type="text" size="4" id="pventa" name="pventa"></td>
                        <td>Nuemro Factura</td>
                        <td><input type="text" size="8" id="numfac" class="required" name="numfac"></td>
                    </tr>
                    <tr>
                        <td colspan="4"></td>
                        <td>Importe Total</td>
                        <td ><input type="text" size="8" id="montofact" readonly name="montof"></td>
                    </tr>
                    <tr>
                        <td colspan="6" align="right"><input type="submit" value="Guardar Factura" id="save"></td>
                    </tr>
                    
             </table>
             </form>
             <br>
                         ';
     $tabla.= "<table width='75%' class='order' align='center'>
                           <thead>
                                <tr>
                                    <th>Fecha de Salida</th>
                                    <th>Hora de Salida</th>
                                    <th>Servicio</th>
                                    <th>Precio Venta</th>
                                    <th>Si/No</th>
                                </tr>
                           </thead>
                           <tbody>";
     while ($data){
           $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
           $tabla.="<tr bgcolor='$color' id='$data[id]' class='modord'>
                                    <td width='10%' align='center'>$data[0]</td>
                                    <td width='10%'>".($data['2'])."</td>
                                    <td width='50%'>".htmlentities($data['1'])."</td>
                                    <td width='20%' align='center'><div id='val$data[4]'>".number_format($data[3], 2, '.', '')."</div></td>
                                    <td width='10%' align='center'><input id='$data[4]' type='checkbox'></td>
                                </tr>";
                    $data = mysql_fetch_array($result);             #FFFFFF#FF0000
                       $j++;
     }
     $tabla.="</tbody></table> </fieldset><br>";

     $tabla.="<style type='text/css'>
                     table.order {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 8pt;
	                              text-align: left;
                               }
                     table.order thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.order tbody td {
	                                        color: #3D3D3D;
	                                        padding: 4px;
	                                        vertical-align: top;
                                         }
                     td.click, th.click{
                                        background-color: #bbb;
                                        }
                     td.hover, tr.hover{
                                        background-color: #69f;
                                        }
                     th.hover, tfoot td.hover{
                                              background-color: ivory;
                                              }
                     td.hovercell, th.hovercell{
                                                background-color: #abc;
                                                }
                     td.hoverrow, th.hoverrow{
                                              background-color: #6df;
                                              }
                     .interno{
                                              background-color: #6df;
                                              }
                     #addfact .error{
	                                 font-size:0.8em;
	                                 color:#ff0000;
                                     }
              </style>
               <script type='text/javascript'>
                                $('#tipoc').selectmenu({width: 250});
                                $('#tipof').selectmenu({width: 50});
                                $('#fechaf').datepicker({dateFormat:'yy-mm-dd'});
                                $('.order').tableHover();
                                $('#save').button();
                                $('#addfact').validate({
                                                            submitHandler: function(){
                                                                                      var sum= calcularImporte();
                                                                                      if (sum > 0){
                                                                                         var ords = '';
                                                                                         $('input:checkbox:checked').each(function(){
                                                                                                                                     var id = $(this).attr('id');
                                                                                                                                     if (ords == '')
                                                                                                                                        ords = id;
                                                                                                                                     else
                                                                                                                                         ords = ords + ','+id;
                                                                                                                                     });
                                                                                         var datos = $('#addfact').serialize()+'&ordenes='+ords+'&cli='+$('#cliente').val()+'&accion=addfc';
                                                                                         alert(datos);
                                                                                         $.post('/modelo/turismo/addnroc.php', datos, function(data){

                                                                                                                                                     var response = $.parseJSON(data);
                                                                                                                                                     alert(response.msge);
                                                                                                                                                     if (response.status){
                                                                                                                                                        alert(response.msge);
                                                                                                                                                     };
                                                                                                                                                     });
                                                                                      }
                                                                                      else
                                                                                          alert('No ha seleccionado ningun servicio a incluir en la factura!!!');
                                                                                      }
                                                       });
                                $('input:checkbox').change(function() {
                                                                  var suma = calcularImporte().toFixed(2);
                                                                  $('#montofact').val(suma);
                                                                  });
                                function calcularImporte(){
                                         var sum=0;
                                         $('input:checkbox:checked').each(function(){
                                                                                     var id = $(this).attr('id');
                                                                                     sum+=parseFloat($('#val'+id).html());
                                                                                     });
                                         return sum;
                                }
                                
                                                       
               </script>";
     @cerrarconexcion($conn);
     print $tabla;
  }
  elseif($accion == 'addfc'){
    $lordenes = explode(',', $_POST['ordenes']);
    $ff = $_POST['fechaf'];
    $pto = $_POST[pventa]?$_POST[pventa]:'null';
    $campos = "id, id_estructura, id_cliente, id_estructura_cliente, fecha_factura, tipo_factura, punto_venta, numero_factura, tipo_comprobante, id_usuario, fecha_alta, ordencompra, monto_factura";
    $values = "$_SESSION[structure], $_POST[cli], $_SESSION[structure], '$ff', '$_POST[tipof]', $pto, $_POST[numfac], $_POST[tipoc], $_SESSION[userid], now(), '$_POST[orco]', $_POST[montof]";
    $conn = conexcion();
    $response = array();     ///respuesta al cliente de la accion requerida
    $response[status] = true;
    try{
        begin($conn);
        $idfact = insert('facturaclientes', $campos, $values, $conn);
        $campos = "id, id_orden, id_estructura_orden, id_factura";
        foreach($lordenes as $orden){
           $values = "$orden, $_SESSION[structure], $idfact";
           insert("ordenesxfactura", $campos, $values, $conn);
        }
        $campos = "id, id_cliente, id_estructura_cliente, importe, fecha_ingreso, id_user, id_factura, fecha_movimiento";
        $values = "$_POST[cli], $_SESSION[structure], $_POST[montof], now(), $_SESSION[userid], $idfact, '$ff'";
        insert('ctacteturismo', $campos, $values, $conn);
        
        commit($conn);
        cerrarconexcion($conn);
        $response[msge] = "Se ha almacenado con exito la factura en la BD!";
        print (json_encode($response));
    }catch (Exception $e) {
                          rollback($conn);
                          cerrarconexcion($conn);
                          $response[status] = false;
                          $response[msge]=$e->getMessage();
                          print (json_encode($response));
                          };
  }
  elseif($accion == 'listar'){
        $sql = "SELECT f.id, date_format(fecha_factura, '%d/%m/%Y') as fecha, tipo, concat(tipo_factura,' ', LPAD(punto_venta,4,'0'), '-',LPAD(numero_factura,8,'0')), count(*)
FROM facturaclientes f
inner join tipo_comprobantes tc on tc.id = f.tipo_comprobante
inner join ordenesxfactura oxf on oxf.id_factura = f.id
inner join ordenes o on o.id = oxf.id_orden and o.id_estructura = oxf.id_estructura_orden
inner join ordenes_turismo ot on ot.id_orden = o.id and ot.id_estructura_orden = o.id_estructura
group by f.id";
  }
  elseif($accion == 'ldpgo'){
     $sql = "SELECT f.id,
                    date_format(fecha_factura, '%d/%m/%Y') as fecha,
                    tipo,
                    concat(tipo_factura,' ', LPAD(punto_venta,4,'0'), '-',LPAD(numero_factura,8,'0')) as comprobante,
                    monto_factura
                         FROM facturaclientes f
                         inner join tipo_comprobantes tc on tc.id = f.tipo_comprobante
                         where id_cliente = $_POST[clientes] and f.id not in (select id_factura FROM facturasxpagosturismo)";
     $tabla.= "<table width='75%' class='order' align='center' id='faccli'>
                           <thead>
                                <tr>
                                    <th>Fecha de factura</th>
                                    <th>Tipo Comprobante</th>
                                    <th>Numero Comprobante</th>
                                    <th>Importe Comprobante</th>
                                    <th>Si/No</th>
                                </tr>
                           </thead>
                           <tbody>";

     $conn = conexcion();
     $result = mysql_query($sql, $conn);
     $data = mysql_fetch_array($result);
     while ($data){
           $color = (($j%2)==0)?'#CFCFCF':'#96B8B6';
           $tabla.="<tr bgcolor='$color' id='$data[id]' class='modord'>
                                    <td width='10%' align='center'>$data[1]</td>
                                    <td width='10%'>".($data['2'])."</td>
                                    <td width='50%'>".htmlentities($data['3'])."</td>
                                    <td width='20%' align='center'><div>".number_format($data[4], 2, '.', '')."</div></td>
                                    <td width='10%' align='center'><input id='$data[0]' type='checkbox'></td>
                                </tr>";
                    $data = mysql_fetch_array($result);             #FFFFFF#FF0000
                       $j++;
     }
     cerrarconexcion($conn);
     $tabla.="</tbody></table> </fieldset><br><style type='text/css'>
                     table.order {
	                              font-family:arial;
	                              background-color: #CDCDCD;
                                  font-size: 8pt;
	                              text-align: left;
                               }
                     table.order thead tr th, table.tablesorter tfoot tr th {
                                                                            background-color: #e6EEEE;
                                                                            border: 1px solid #FFF;
	                                                                        font-size: 8pt;
	                                                                        padding: 4px;}
                     table.order tbody td {
	                                        color: #3D3D3D;
	                                        padding: 4px;
	                                        vertical-align: top;
                                         }
                     td.click, th.click{
                                        background-color: #bbb;
                                        }
                     td.hover, tr.hover{
                                        background-color: #69f;
                                        }
                     th.hover, tfoot td.hover{
                                              background-color: ivory;
                                              }
                     td.hovercell, th.hovercell{
                                                background-color: #abc;
                                                }
                     td.hoverrow, th.hoverrow{
                                              background-color: #6df;</style>";
     print $tabla;
  }
  elseif($accion == 'svepgo'){
    $lfacts = explode(',', $_POST['facts']);
    $conn = conexcion();
    $response = array();     ///respuesta al cliente de la accion requerida
    $response[status] = true;
    $campos = "id, id_cliente, id_estructura_cliente, descripcion, importe, id_user, fecha_alta";
    $values = "$_POST[clientes], $_SESSION[structure], '', $_POST[importe], $_SESSION[userid], now()";
    try{
        begin($conn);
        $idpago = insert('pagosturismo', $campos, $values, $conn);
        $campos = "id, id_factura, id_pago";
        foreach($lfacts as $fact){
           $values = "$fact, $idpago";
           insert("facturasxpagosturismo", $campos, $values, $conn);
        }
        $campos = "id, id_cliente, id_estructura_cliente, importe, fecha_ingreso, id_user, id_pago, fecha_movimiento";
        $values = "$_POST[clientes], $_SESSION[structure], $_POST[importe], now(), $_SESSION[userid], $idpago, '$_POST[fpago]'";
        insert('ctacteturismo', $campos, $values, $conn);

        commit($conn);
        cerrarconexcion($conn);
        $response[msge] = "Se ha almacenado con exito el pago en la BD!";
        print (json_encode($response));
    }catch (Exception $e) {
                          rollback($conn);
                          cerrarconexcion($conn);
                          $response[status] = false;
                          $response[msge]=$e->getMessage();
                          print (json_encode($response));
                          };

  }
?>

