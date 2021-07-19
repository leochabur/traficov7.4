<?php
  session_start();
  include ($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelo/enviomail/sendmail.php');
  define(STRUCTURED, $_SESSION['structure']);
  
  $accion = $_POST['accion'];
  if ($accion == 'soe'){ //codigo para guardar una orden de un vacio
              $estructura = STRUCTURED;
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";
              
              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";
              
              $nombre = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";
              
              $cliente = $_POST['cliente_vacio'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";
              
              $corresponde_a = $_POST['corresponde_a'];
              $campos.=",id_cliente_vacio, id_estructura_cliente_vacio";
              $values.=",$corresponde_a, $estructura";
              
              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";
              
              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";
              
              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";
              
              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";
              
              $hfins = $_POST['hfins'];
              $campos.=",hfinservicio";
              $values.=",'$hfins'";
              
              $km = $_POST['km'];
              $campos.=",km, vacio";
              $values.=",$km, 1";
              
              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }
              echo insert('ordenes', "id, $campos", $values);

  }elseif ($accion == 'orev'){
              
              $estructura = STRUCTURED;
              
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];

              $campos.=",fservicio, id_claseservicio, id_estructuraclaseservicio";
              $values.=",'$fecha', $_POST[clase], $estructura";

              $nombre = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";


              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";

              $hfins = $_POST['hfins'];
              $campos.=",hfinservicio";
              $values.=",'$hfins'";
              
              $hllegada = $_POST['hllegada'];
              $campos.=",hllegada";
              $values.=",'$hllegada'";
              
              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $chofer = $_POST['conductor'];
              if ($chofer){
                 $campos.=",id_chofer_1, id_estructura_chofer1";
                 $values.=",$chofer, $estructura";
              }
              
              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }
              echo insert('ordenes', "id, $campos", $values);

  }elseif ($accion == 'soser'){ //codigo para guarar una orden de un servicio en la BD
              $conn = conexcion();
              $estructura = STRUCTURED;

              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";

              $servicio = $_POST['horario'];
              $campos.=",id_servicio, id_estructura_servicio";
              $values.=", $servicio, $estructura";
              
              $sql = "SELECT c.nombre, c.ciudades_id_origen, c.ciudades_id_destino, c.vacio, c.id_cliente_vacio, c.id_estructura_cliente_vacio
                      FROM servicios s
                      INNER JOIN cronogramas c on ((c.id = s.id_cronograma)and(c.id_estructura = s.id_estructura_cronograma))
                      WHERE ((s.id = $servicio)and(s.id_estructura = $estructura))";
              $result = mysql_query($sql, $conn); //recupera el nombre del cronograma para asociarlo a la orden
              if ($data = mysql_fetch_array($result)){
                 $nombre  = $data['nombre'];
                 $origen  = $data['ciudades_id_origen'];
                 $destino = $data['ciudades_id_destino'];
                 $vacio = $data['vacio'];
                 if ($vacio == 1){
                    $campos.=", vacio";
                    $values.=", $vacio";
                    if (!($data['id_cliente_vacio'] === NULL)){
                       $campos.=", id_cliente_vacio, id_estructura_cliente_vacio";
                       $values.=", $data[id_cliente_vacio], $data[id_estructura_cliente_vacio]";
                    }
                    
                 }
              }
              mysql_close($conn);
              
              $campos.=",nombre";
              $values.=",'$nombre'";
              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";

              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $chofer = $_POST['conductor'];
              if ($chofer){
                 $campos.=",id_chofer_1, id_estructura_chofer1";
                 $values.=",$chofer, $estructura";
              }

              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";
              
              $hllegada = $_POST['hllegada'];
              $campos.=",hllegada";
              $values.=",'$hllegada'";
              
              $hfinserv = $_POST['hfinserv'];
              $campos.=",hfinservicio";
              $values.=",'$hfinserv'";

              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }
              
              echo insert('ordenes', "id, $campos", $values);

  }elseif ($accion == 'soes'){

              $estructura = STRUCTURED;

              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fservicio'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $campos.=",fservicio";
              $values.=",'$fecha'";

              $nombre  = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $cli_vacio = $vacio = getOpcion('cliente-vacio', $_SESSION['structure']);
              $campos.=",id_cliente, id_estructura_cliente, vacio";
              $values.=", $cli_vacio, $estructura, 1";
              
              $clientevacio = $_POST['corresponde'];
              $campos.=",id_cliente_vacio, id_estructura_cliente_vacio";
              $values.=",$clientevacio, $estructura";

              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $chofer = $_POST['conductor'];
              if ($chofer){
                 $campos.=",id_chofer_1, id_estructura_chofer1";
                 $values.=",$chofer, $estructura";
              }

              $hcitacion = $_POST['hcitacion'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";

              $hfinserv = $_POST['hfinserv'];
              $campos.=",hfinservicio";
              $values.=",'$hfinserv'";

              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $interno = $_POST['interno'];
              if ($interno){
                 $campos.=",id_micro";
                 $values.=",$interno";
              }

              echo insert('ordenes', "id, $campos", $values);

  }elseif ($accion == 'deor'){ //codigo para eliminar una orden
              $id = $_POST['order'];
              $estructura = STRUCTURED;
              $campo='borrada, id_user, fecha_accion';
              $value="1, $_SESSION[userid], now()";
              backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
              update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
  }elseif ($accion == 'sendodate'){ //CODIGO PARA ENVIAR UNA ORDEN A OTRA FECHA
              $estructura = STRUCTURED;
              $fecha = $_POST['fecha'];
              $id = $_POST['order'];
              
              $campo='fservicio, id_user, fecha_accion';
              $value="'$fecha', $_SESSION[userid], now()";
              backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
              update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
  }elseif ($accion == 'chemcli'){ //CODIGO PCAMBIAR EL CLIENTE AL CUAL SE LE AFECTA UN VACIO
              $estructura = STRUCTURED;
              $cliente = $_POST['cli'];
              $id = $_POST['order'];
              $sql =     "SELECT * FROM ordenes where (id = $id) and (id_estructura = $estructura) and (vacio)";
              $sql_cli = "SELECT razon_social FROM clientes c where id = $cliente and id_estructura = ".STRUCTURED;
              $conn = conexcion();
              $result = mysql_query($sql, $conn);
              $result_cli = mysql_query($sql_cli, $conn);
              mysql_close($conn);
              if(mysql_num_rows($result) > 0){
                      $data = mysql_fetch_array($result);
                      $crono = $data['nombre'];
                      $data_cli = mysql_fetch_array($result_cli);
                      $nom_cliente = $data_cli['razon_social'];
                      $pos = strpos($crono, '~');
                      if ($pos){
                         $crono=substr_replace($crono, "($nom_cliente)", ($pos+1), strlen($crono));
                      }
                      else{
                           $crono.= "~($nom_cliente)";
                      }
                      $campo='nombre, id_cliente_vacio, id_estructura_cliente_vacio, id_user, fecha_accion';
                      $value="'$crono', $cliente, $estructura, $_SESSION[userid], now()";
                      backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
                      update('ordenes', $campo, $value, "(id = '".$id."') and (id_estructura = ".STRUCTURED.")");
                      print $crono;
              }
  }elseif ($accion == 'sortur'){ //guarda una orden cargada desde turismo
              $estructura = STRUCTURED;
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fsalida'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];

              $campos.=",fservicio, id_claseservicio, id_estructuraclaseservicio";
              $values.=",'$fecha', $_POST[clase], $estructura";

              $nombre = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";


              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";


              $hcitacion = $_POST['hsalida'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";
              
              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";
              
              $hllegada = $_POST['hllegada'];
              $campos.=",hllegada";
              $values.=",'$hllegada'";

              $hfins = $_POST['hllegada'];
              $campos.=",hfinservicio";
              $values.=",'$hfins'";

              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              $orden = insert('ordenes', "id, $campos", $values);
              
              $campos = "id, id_orden, precio_venta_neto, precio_venta_final, viaticos, id_estructura_orden";
              $values = "$orden, $_POST[preciosiva], $_POST[preciofinal], $_POST[viaticos], $estructura";
              
              $campos.=",contacto, tel_contacto, mail_contacto";
              $values.=",'$_POST[nomcontacto]','$_POST[telcontacto]','$_POST[mailcontacto]'";
              
              $campos.=",lugar_salida, lugar_llegada, capacidad_solicitada, hora_regreso";
              $values.=",'$_POST[lugarsalida]','$_POST[lugarllegada]',$_POST[pax], '$_POST[hsalidaregreso]'";
              
              $orden_turismo = insert('ordenes_turismo', $campos, $values);
              
              $otur = $orden_turismo;
              $campos_ctacte = "id, id_orden, id_estructura_orden, id_cliente, id_estructura_cliente, importe, viaje_pago, fecha_ingreso, id_user";
              $values_ctacte = "$orden_turismo, $estructura, $_POST[cliente], $estructura, $_POST[preciofinal], 'v', now(), $_SESSION[userid]";
              $orden_turismo = insert('ctacteturismo', $campos_ctacte, $values_ctacte);
              
              $result = ejecutarSQL("SELECT razon_social,
                                            date_format(fservicio, '%d/%m/%Y'),
                                            hsalida,
                                            capacidad_solicitada,
                                            concat(upper(ori.ciudad), ' (',lugar_salida,')'),
                                            concat(upper(des.ciudad), ' (',lugar_llegada,')'),
                                            contacto,
                                            tel_contacto
                                     FROM ordenes_turismo ot
                                     inner join ordenes o on o.id = ot.id_orden
                                     inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
                                     inner join ciudades ori on ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
                                     inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
                                     where ot.id = $otur");
              if ($data = mysql_fetch_array($result)){
                 $mail = "Cliente: $data[0]<br>Fecha: $data[1]  Hora Salida: $data[2]<br> Capacidad Solicitada: $data[3]<br>
                          Origen: $data[4]  Destino:$data[5]<br> Contacto: $data[6]  Tel. Contacto: $data[7]";
                 enviarMail("rdattoli@masterbus.net, mdepeon@masterbus.net, raguiar@masterbus.net, lemartin@masterbus.net, leochabur@gmail.com", $mail, "Nueva Orden de Turismo Generada");
                 
              }
              
  }elseif ($accion == 'mortur'){ //modifica una orden cargada desde turismo
              $estructura = STRUCTURED;
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";

              $fecha = $_POST['fsalida'];
              $fecha = explode("/", $fecha);
              $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
              
              $fechar = $_POST['fregreso'];
              $fechar = explode("/", $fechar);
              $fechar = $fechar[2].'-'.$fechar[1].'-'.$fechar[0];

              $campos.=",fservicio, id_claseservicio, id_estructuraclaseservicio";
              $values.=",'$fecha', $_POST[clase], $estructura";

              $nombre = $_POST['nombre'];
              $campos.=",nombre";
              $values.=",'$nombre'";

              $cliente = $_POST['cliente'];
              $campos.=",id_cliente, id_estructura_cliente";
              $values.=",$cliente, $estructura";

              $origen = $_POST['origen'];
              $campos.=",id_ciudad_origen, id_estructura_ciudad_origen";
              $values.=",$origen, $estructura";

              $destino = $_POST['destino'];
              $campos.=",id_ciudad_destino, id_estructura_ciudad_destino";
              $values.=",$destino, $estructura";

              $hcitacion = $_POST['hsalida'];
              $campos.=",hcitacion";
              $values.=",'$hcitacion'";

              $hsalida = $_POST['hsalida'];
              $campos.=",hsalida";
              $values.=",'$hsalida'";

              $hllegada = $_POST['hllegada'];
              $campos.=",hllegada";
              $values.=",'$hllegada'";

              $hfins = $_POST['hllegada'];
              $campos.=",hfinservicio";
              $values.=",'$hfins'";

              $km = $_POST['km'];
              $campos.=",km";
              $values.=",$km";

              backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")");
              update('ordenes', $campos, $values, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")");
              


              $campos = "precio_venta_neto, precio_venta_final, viaticos, id_estructura_orden";
              $values = "$_POST[preciosiva], $_POST[preciofinal], $_POST[viaticos], $estructura";
              $campos.=",contacto, tel_contacto, mail_contacto";
              $values.=",'$_POST[nomcontacto]','$_POST[telcontacto]','$_POST[mailcontacto]'";
              $campos.=",lugar_salida, lugar_llegada, capacidad_solicitada, hora_regreso, fecha_regreso";
              $values.=",'$_POST[lugarsalida]','$_POST[lugarllegada]',$_POST[pax], '$_POST[hsalidaregreso]', '$fechar'";
              
              //die("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)");
              $result = ejecutarSQL("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)");

              if ($data = mysql_fetch_array($result)){ //se ha agregado el registro a la tabla ordenes_turismo
                 $orden_mod=$data[0];
                 update('ordenes_turismo', $campos, $values, "(id = $data[0])");
              }
              else{
                 $orden_mod=insert('ordenes_turismo', "id, id_orden, $campos", "$_POST[orden], $values");

              }

              $campos_ctacte = "id_cliente, id_estructura_cliente, importe, viaje_pago, fecha_ingreso, id_user";
              $values_ctacte = "$_POST[cliente], $estructura, $_POST[preciofinal], 'v', now(), $_SESSION[userid]";
              $result = ejecutarSQL("SELECT id FROM ctacteturismo WHERE (id_orden = $orden_mod)");
              if ($data = mysql_fetch_array($result)){
                 update('ctacteturismo', $campos_ctacte, $values_ctacte, "(id = $data[0])");
              }
              else{
                   insert('ctacteturismo', "id, id_orden, id_estructura_orden,".$campos_ctacte, "$orden_mod, $estructura, $values_ctacte");
              }

  }
?>
