<?php
  session_start();
  if (!$_SESSION['auth']){
     print "<br><b>La sesion a expirado</b>";
     exit;
  }
  date_default_timezone_set('America/New_York');
  include ($_SERVER['DOCUMENT_ROOT'].'/controlador/bdadmin.php');
  include($_SERVER['DOCUMENT_ROOT'].'/controlador/ejecutar_sql.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelo/enviomail/sendmail.php');
  include($_SERVER['DOCUMENT_ROOT'].'/modelo/utils/dateutils.php');
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

              $campos.=", id_claseservicio, id_estructuraclaseservicio";
              $values.=", $_POST[clase], $estructura";

              $conn = conexcion();
              $response = array();     ///respuesta al cliente de la accion requerida
              $response[status] = true;
              
              ////////////datos correspondientes a la salida y llegada del servicio//////////////////7
              $fecha_ida =  dateToMysql($_POST['fsalida'],'/');
              $hora_salida_ida = $_POST['hsalida'];
              $hora_llegada_ida = $_POST['hllegada'];
              $salida_servicio = new DateTime("$fecha_ida");

              if (! isset($_POST['srg'])){
                 $fecha_regreso =  dateToMysql($_POST['fregreso'],'/');
                 $hora_salida_regreso = $_POST['hsalidaregreso'];
                 $hora_llegada_regreso = $_POST['hllegadaregreso'];
                 $regreso_servicio = new DateTime("$fecha_regreso");
              }
              ////////////////////////////////////////////////////////////////////////////////////
              
              $ahora = new DateTime("now");
              if ($ahora > $salida_servicio){
                 $response[status] = false;
                 $response[msge] = "La fecha del servicio no puede ser anterior a la fecha actual!!";
                 print (json_encode($response));
                 exit();
              }
              
              if (! isset($_POST['srg'])){
                 if ($salida_servicio > $regreso_servicio){
                    $response[status] = false;
                    $response[msge] = "La fecha de regreso no puede ser anterior a la fecha de salida!!";
                    print (json_encode($response));
                    exit();
                 }
              }

              
              try{
                  begin($conn);
                  
                  if (! isset($_POST['srg'])){
                     $interval = $salida_servicio->diff($regreso_servicio);
                     $dias = $interval->format('%d');
                  }
                  else{
                       $dias = 0;
                  }

                  ////campos orden de turismo//////////////////////////////////////////////
                  $campos_turismo= "id, viaticos";
                  $values_turismo=($_POST[viaticos]==''?'NULL':$_POST[viaticos]);

                  $campos_turismo.=", bar, banio, tv, mantas, microfono, mov_dest";
                  $values_turismo.=", ".(isset($_POST[bar])?1:0).', '.(isset($_POST[banio])?1:0).','.(isset($_POST[dvd])?1:0).','.(isset($_POST[mantas])?1:0).','.(isset($_POST[mic])?1:0).','.(isset($_POST[excur])?1:0);
                     
                  $campos_turismo.=", contacto, tel_contacto, mail_contacto, observaciones, pago_anticipado";
                  $values_turismo.=",'$_POST[nomcontacto]','$_POST[telcontacto]','$_POST[mailcontacto]', '".str_replace(",",";",$_POST[observa])."',".(isset($_POST[pagoanti])?1:0);
                  
                  $fec_reg = (!isset($_POST['srg']))?"'".$regreso_servicio->format('Y-m-d')."'":"NULL";
                  
                  $campos_turismo.=", lugar_salida, lugar_llegada, capacidad_solicitada, fecha_regreso, hora_regreso, hora_llegada_regreso";
                  $values_turismo.=", '$_POST[lugarsalida]','$_POST[lugarllegada]',$_POST[pax], $fec_reg, ".(!isset($_POST['srg'])?"'$_POST[hsalidaregreso]'":"NULL").", ".(!isset($_POST['srg'])?"'$_POST[hllegadaregreso]'":"NULL");
                  /////////////////////////////////////////////////////////////////////////////7

                  $km = $_POST['km']?$_POST['km']:0;
                  $cliente = $_POST['cliente'];
                  if ($dias == 0){ //significa que el servicio sale y regresa el mismo dia

                     $km=($km*2);
                     $campos.=", id_cliente, id_estructura_cliente, km, nombre, hsalida, hcitacion , hllegada, hfinservicio,id_ciudad_origen, id_estructura_ciudad_origen,id_ciudad_destino, id_estructura_ciudad_destino";
                     $values.=", $cliente, $estructura, $km, '$_POST[nombre]', '$hora_salida_ida', '$hora_salida_ida', '".(isset($_POST['srg'])?$hora_llegada_ida:$hora_llegada_regreso)."', '".(isset($_POST['srg'])?$hora_llegada_ida:$hora_llegada_regreso)."', $_POST[origen], $estructura ,$_POST[destino], $estructura";

                     $campos.=", fservicio";
                     $values.=", '".$salida_servicio->format('Y-m-d')."'";

                     $response[msge] = "Se ha generado un error al intentar guardar la orden!";
                     $orden = insert('ordenes', "id, $campos", $values, $conn); //inserta la orden
                     
                     $price = ($_POST[preciofinal]==''?'NULL':$_POST[preciofinal]);
                     $campos_turismo.= ", id_orden, id_estructura_orden, precio_venta_final, efc, afecta_ctacte";
                     $values_turismo.= ", $orden, $estructura, $price, ".(isset($_POST[efc])?1:0).", 1";
                     
                     $response[msge] = "Se ha generado un error al intentar guardar la orden de turismo!";
                     $orden_turismo = insert('ordenes_turismo', $campos_turismo, $values_turismo, $conn);
                  //   die(json_encode($response));
                  }
                  else{
                       $campos.=", id_cliente, id_estructura_cliente, km, nombre, hsalida, hcitacion , hllegada, hfinservicio,id_ciudad_origen, id_estructura_ciudad_origen,id_ciudad_destino, id_estructura_ciudad_destino";
                       $values.=", $cliente, $estructura, $km, '$_POST[nombre] (IDA)', '$hora_salida_ida', '$hora_salida_ida', '$hora_llegada_ida', '$hora_llegada_ida',$_POST[origen], $estructura ,$_POST[destino], $estructura";

                       $campos.=", fservicio";
                       $values.=", '".$salida_servicio->format('Y-m-d')."'";

                       $response[msge] = "Se ha generado un error al intentar guardar la orden del servicio de IDA!";
                       $orden = insert('ordenes', "id, $campos", $values, $conn); //inserta la orden del servicio de ida
                       
                       //solo genera ordenes de turismo para el servicio de ida, el resto no
                       $price = ($_POST[preciofinal]==''?'NULL':$_POST[preciofinal]);
                       $campos_turismo.= ", id_orden, id_estructura_orden, precio_venta_final, efc, afecta_ctacte";
                       $values_turismo.= ", $orden, $estructura, $price, ".(isset($_POST[efc])?1:0).", 1";

                       $response[msge] = "Se ha generado un error al intentar guardar la orden de turismo !";
                       $orden_turismo = insert('ordenes_turismo', $campos_turismo, $values_turismo, $conn);
                       
                   //    $response[sql] = "$campos";
                 //      die(json_encode($response));
                       for ($i=1; $i < $dias; $i++){
                           $salida_servicio->add(new DateInterval('P1D'));
                           $values = "$estructura, $_SESSION[userid], $_POST[clase], $estructura, $cliente, $estructura, 0, 'EN VIAJE', '00:00', '00:00', '23:59', '23:59',$_POST[destino], $estructura ,$_POST[destino], $estructura, '".$salida_servicio->format('Y-m-d')."'";

                           $response[msge] = "Se ha generado un error al intentar guardar la orden en viaje del dia $i!!!";
                           $orden_anexas = insert('ordenes', "id, $campos", $values, $conn);
                           ///asocia cada orden a la orden de turismo primera, para que se pueda acceder a que servicios armo la orden de turismo
                           insert('ordenes_asocioadas', "id, id_orden, id_estructura_orden, id_orden_asociada, id_esructura_orden_asociada", "$orden, $estructura, $orden_anexas, $estructura", $conn);
                       }
                       //en el bucle anterior genera todas las ordenes que representan que el coche se encuentra en destino
                       ///lo que falta generar es el servicio de regreso desde el destino
                       $salida_servicio->add(new DateInterval('P1D'));
                       $values = "$estructura, $_SESSION[userid], $_POST[clase], $estructura, $cliente, $estructura, $km, '$_POST[nombre] (REGRESO)', '$hora_salida_regreso', '$hora_salida_regreso', '$hora_llegada_regreso', '$hora_llegada_regreso',$_POST[destino], $estructura ,$_POST[origen], $estructura, '".$salida_servicio->format('Y-m-d')."'";
                       $orden_anexas = insert('ordenes', "id, $campos", $values, $conn);
                       insert('ordenes_asocioadas', "id, id_orden, id_estructura_orden, id_orden_asociada, id_esructura_orden_asociada", "$orden, $estructura, $orden_anexas, $estructura", $conn);
                  }
                  $sql = "SELECT id FROM items_gastos_turismo i order by detalle";
                  $result = ejecutarSQL($sql, $conn);
                  while ($row = mysql_fetch_array($result)){
                        if (isset($_POST["gas$row[id]"])){
                           insert("gastos_por_servicio_turismo", "id, id_item_gasto, id_orden, id_estructura_orden", "$row[0], $orden, $estructura", $conn);
                        }
                  }

              $otur = $orden_turismo;
              

              $campos_ctacte = "id, id_orden, id_estructura_orden, id_cliente, id_estructura_cliente, importe, viaje_pago, fecha_ingreso, id_user";
              $values_ctacte = "$orden_turismo, $estructura, $_POST[cliente], $estructura, $price, 'v', now(), $_SESSION[userid]";

              if ($_POST[preciofinal]){
                 $orden_turismo = insert('ctacteturismo', $campos_ctacte, $values_ctacte, $conn);
              }
              
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
                                     where ot.id = $otur", $conn);
              if ($data = mysql_fetch_array($result)){
                 $mail = "Cliente: $data[0]<br>Fecha: $data[1]  Hora Salida: $data[2]<br> Capacidad Solicitada: $data[3]<br>
                          Origen: $data[4]  Destino:$data[5]<br> Contacto: $data[6]  Tel. Contacto: $data[7]";

                 enviarMail("leochabur@gmail.com, kbreitenberger@masterbus.net, rdattoli@masterbus.net, mdepeon@masterbus.net, raguiar@masterbus.net, lemartin@masterbus.net, turismo@masterbus.net, rpizzutti@masterbus.net ", $mail, "Nueva Orden de Turismo Generada");
                 
              }
              commit($conn);
              cerrarconexcion($conn);
              $response[msge] = "Se ha generado con exito la orden!";
              print (json_encode($response));
              }catch (Exception $e) {
                                      rollback($conn);
                                      cerrarconexcion($conn);
                                      $response[status] = false;
                                      $response[msge]=$e->getMessage();
                                      print (json_encode($response));
                                      };
              
  }elseif ($accion == 'mortur'){ //modifica una orden cargada desde turismo
  $estructura = STRUCTURED;
      //        enviarMail("leochabur@gmail.com, leochabur@gmail.com", "probandoooooooooo", "ORDEN DE TURISMO MODIFICADA");
        //      die();
     if(!$_POST[realizada]){ //si el servicio aun no se ha llevado a cabo modifica, tanto la orden como la orden de turismo

              $fecha_ida =  dateToMysql($_POST['fsalida'],'/');
              $hora_salida_ida = $_POST['hsalida'];
              $hora_llegada_ida = $_POST['hllegada'];

              $fecha_regreso =  dateToMysql($_POST['fregreso'],'/');
              $hora_salida_regreso = $_POST['hsalidaregreso'];
              $hora_llegada_regreso = $_POST['hllegadaregreso'];
              
              $campos = "id_estructura, id_user";
              $values = "$estructura, $_SESSION[userid]";


              $campos.=",fservicio, id_claseservicio, id_estructuraclaseservicio";
              $values.=",'$fecha_ida', $_POST[clase], $estructura";

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
  
              /////////////////////////////////////////////////////////
              $fecha_ida =  dateToMysql($_POST['fsalida'],'/');
              $hora_salida_ida = $_POST['hsalida'];
              $hora_llegada_ida = $_POST['hllegada'];

              $fecha_regreso =  dateToMysql($_POST['fregreso'],'/');
              $hora_salida_regreso = $_POST['hsalidaregreso'];
              $hora_llegada_regreso = $_POST['hllegadaregreso'];

              $salida_servicio = new DateTime("$fecha_ida");
              $regreso_servicio = new DateTime("$fecha_regreso");
              $conn = conexcion();
              $response = array();     ///respuesta al cliente de la accion requerida
              $response[status] = true;
              try{
                  begin($conn);
              
                  $sql = "SELECT razon_social,
                                 date_format(o.fservicio, '%d/%m/%Y') as fservicio,
                                 hsalida,
                                 capacidad_solicitada,
                                 concat(upper(ori.ciudad), ' (',lugar_salida,')') as origen,
                                 concat(upper(des.ciudad), ' (',lugar_llegada,')') as destino,
                                 contacto,
                                 tel_contacto,
                                 date_format(fecha_regreso, '%d/%m/%Y') as fregreso,
                                 hora_regreso
                          FROM ordenes_turismo ot
                          left join ordenes o on o.id = ot.id_orden
                          inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
                          inner join ciudades ori on ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
                          inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
                          where o.id = $_POST[orden]";
                  $result = ejecutarSQL($sql, $conn);
                  if ($data = mysql_fetch_array($result)){
                           $mail = "<b><i><u>DATOS ORIGINALES DE LA ORDEN </u></i></b><br>
                                     Cliente: $data[0]
                                     Fecha: $data[1]
                                     Hora Salida: $data[2]<br>
                                     Capacidad Solicitada: $data[3]<br>
                                     Origen: $data[4]  Destino:$data[5]<br>
                                     Contacto: $data[6]
                                     Tel. Contacto: $data[7]<br>
                                     Fecha Regreso: $data[8] Hora Regreso: $data[9]";
                  }


                  backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                  update('ordenes', $campos, $values, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                  $campos = "precio_venta_final, viaticos, id_estructura_orden";
                  $values = "$_POST[preciofinal], ".($_POST[viaticos]==''?'NULL':$_POST[viaticos]).", $estructura";
                  $campos.=",contacto, tel_contacto, mail_contacto";
                  $values.=",'".str_replace(",",";",$_POST[nomcontacto])."','$_POST[telcontacto]','$_POST[mailcontacto]'";
                  $campos.=",lugar_salida, lugar_llegada, capacidad_solicitada, hora_regreso, fecha_regreso, afecta_ctacte, efc";
                  $values.=",'$_POST[lugarsalida]','$_POST[lugarllegada]',$_POST[pax], '$_POST[hsalidaregreso]', '$fecha_regreso', 1,".(isset($_POST[efc])?1:0);

                  $campos.=", bar, banio, tv, mantas, microfono, mov_dest, observaciones";
                  $values.=", ".(isset($_POST[bar])?1:0).', '.(isset($_POST[banio])?1:0).','.(isset($_POST[dvd])?1:0).','.(isset($_POST[mantas])?1:0).','.(isset($_POST[mic])?1:0).','.(isset($_POST[excur])?1:0).", '".str_replace(",",";",$_POST[observa])."'";


                  //die("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)");
                  $result = ejecutarSQL("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)", $conn);

                  if ($data = mysql_fetch_array($result)){ //se ha agregado el registro a la tabla ordenes_turismo
                     $orden_mod=$data[0];
                     update('ordenes_turismo', $campos, $values, "(id = $data[0])", $conn);
                  }
                  else{
                       $orden_mod=insert('ordenes_turismo', "id, id_orden, $campos", "$_POST[orden], $values", $conn);
                  }
                  
                  delete("gastos_por_servicio_turismo", "id_orden, id_estructura_orden", "$_POST[orden], $estructura", $conn); //elimina todos los gatos asociados para volver a dar de alta los modificados
                  $sql = "SELECT id FROM items_gastos_turismo i order by detalle";
                  $result = ejecutarSQL($sql, $conn);
                  while ($row = mysql_fetch_array($result)){
                        if (isset($_POST["gas$row[id]"])){
                           insert("gastos_por_servicio_turismo", "id, id_item_gasto, id_orden, id_estructura_orden", "$row[0], $_POST[orden], $estructura", $conn);
                        }
                  }
                  
                  $campos_ctacte = "id_cliente, id_estructura_cliente, importe, viaje_pago, fecha_ingreso, id_user";
                  $values_ctacte = "$_POST[cliente], $estructura, $_POST[preciofinal], 'v', now(), $_SESSION[userid]";
                  $result = ejecutarSQL("SELECT id FROM ctacteturismo WHERE (id_orden = $orden_mod)", $conn);
                  if ($data = mysql_fetch_array($result)){
                     update('ctacteturismo', $campos_ctacte, $values_ctacte, "(id = $data[0])", $conn);
                  }
                  else{
                       insert('ctacteturismo', "id, id_orden, id_estructura_orden,".$campos_ctacte, "$orden_mod, $estructura, $values_ctacte", $conn);
                  }
                  commit($conn);
                  
                  ///una vez que actualizo las ordenes sin errores, anexa al mail, los nuevos datos de la orden
                  $sql = "SELECT razon_social,
                                 date_format(o.fservicio, '%d/%m/%Y') as fservicio,
                                 hsalida,
                                 capacidad_solicitada,
                                 concat(upper(ori.ciudad), ' (',lugar_salida,')') as origen,
                                 concat(upper(des.ciudad), ' (',lugar_llegada,')') as destino,
                                 contacto,
                                 tel_contacto,
                                 date_format(fecha_regreso, '%d/%m/%Y') as fregreso,
                                 hora_regreso
                          FROM ordenes_turismo ot
                          left join ordenes o on o.id = ot.id_orden
                          inner join clientes c on c.id = o.id_cliente and c.id_estructura = o.id_estructura_cliente
                          inner join ciudades ori on ori.id = o.id_ciudad_origen and ori.id_estructura = o.id_estructura_ciudad_origen
                          inner join ciudades des on des.id = o.id_ciudad_destino and des.id_estructura = o.id_estructura_ciudad_destino
                          where o.id = $_POST[orden]";
                  $result = ejecutarSQL($sql, $conn);
                  if ($data = mysql_fetch_array($result)){
                           $mail.= "<br><br><b><i><u>NUEVOS DATOS DE LA ORDEN </u></i></b><br>
                                     Cliente: $data[0]<br>
                                     Fecha: $data[1]
                                     Hora Salida: $data[2]<br>
                                     Capacidad Solicitada: $data[3]<br>
                                     Origen: $data[4]  Destino:$data[5]<br>
                                     Contacto: $data[6]
                                     Tel. Contacto: $data[7]<br>
                                     Fecha Regreso: $data[8] Hora Regreso: $data[9]";
                  }
                  enviarMail("leochabur@gmail.com, kbreitenberger@masterbus.net, rdattoli@masterbus.net, mdepeon@masterbus.net, raguiar@masterbus.net, lemartin@masterbus.net, turismo@masterbus.net, rpizzutti@masterbus.net ", $mail, "ORDEN DE TURISMO MODIFICADA");
                  $response[msge] = "Se ha actualizado con exito la orden!";
                  cerrarconexcion($conn);
                  print (json_encode($response));
              }catch (Exception $e) {
                                      $response[status] = false;
                                      $response[msge]="No se ha podido realizar la modificacion sokicitada!!!";
                                      $response[sql]=$e->getMessage();
                                      rollback($conn);
                                      cerrarconexcion($conn);
                                      print (json_encode($response));
                                      };
              
              
              ////////////////////////////////////////////////////////

        /*      $sql = "SELECT * FROM ordenes o where id = $_POST[orden]";
              $conn = conexcion();
              $result = ejecutarSQL($sql, $conn);
              if ($row = mysql_fetch_array($result)){   //levantra la orden para corroborar si es valido el cambio de fecha
                 $salida_original = new DateTime("$row[fservicio]");
                 $ahora = new DateTime("now");
                 if ($salida_original == $salida_servicio){
                    $response = array();
                    $response[msge]="no cambiooooooooooooooooooooooooooooooooo";
                    die(json_encode($response));
                 }
                 elseif ($salida_servicio <  $ahora){
                         $response = array();
                         $response[msge]="no se puede antes de hoy!!!!!!!!!";
                         die(json_encode($response));


                 }


              }


             */


      }
      else{ //si el servicio ya se realizo solo modifica valores de venta del viaje y viaticos de conductor
              $conn = conexcion();
              $response = array();     ///respuesta al cliente de la accion requerida
              $response[status] = true;
              try{
                       begin($conn);
              $campos = "precio_venta_final, viaticos, afecta_ctacte, efc, id_estructura_orden";
              $values = "$_POST[preciofinal], $_POST[viaticos], 1, ".(isset($_POST[efc])?1:0).", $estructura";
            //  $campos.=",contacto, tel_contacto, mail_contacto";
            //  $values.=",'$_POST[nomcontacto]','$_POST[telcontacto]','$_POST[mailcontacto]'";
           //   $campos.=",lugar_salida, lugar_llegada, capacidad_solicitada, hora_regreso, fecha_regreso";
           //   $values.=",'$_POST[lugarsalida]','$_POST[lugarllegada]',$_POST[pax], '$_POST[hsalidaregreso]', '$fechar'";
              $result = ejecutarSQL("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)", $conn);

              if ($data = mysql_fetch_array($result)){ //se ha agregado el registro a la tabla ordenes_turismo
                 $orden_mod=$data[0];
                 update('ordenes_turismo', $campos, $values, "(id = $data[0])", $conn);
              }
              else{
                 $orden_mod=insert('ordenes_turismo', "id, id_orden, $campos", "$_POST[orden], $values", $conn);
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
              
              
              
              commit($conn);
                            $response[msge] = "Se ha generado con exito la orden!";
              print (json_encode($response));
              }catch (Exception $e) {
                                      rollback($conn);
                                      cerrarconexcion($conn);
                                      $response[status] = false;
                                      $response[msge]="No se ha podido realizar la modificacion sokicitada!!!";
                                      $response[sql]=$e->getMessage();
                                      print (json_encode($response));
                                      };
      }




  }
?>
