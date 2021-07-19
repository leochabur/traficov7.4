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
          //    die(var_dump($conn));
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
                 if (($_SESSION['userid'] != 25) && ($_SESSION['userid'] != 33)) {
                    $response[status] = false;
                    $response[msge] = "La fecha del servicio no puede ser anterior a la fecha actual!!";
                    print (json_encode($response));
                    exit();
                 }
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
                       if (!isset($_POST['srg'])){
                          procesarOrdenTurismo($orden_anexas, $fecha_regreso, 0, $_POST, $conn);
                       }
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
              //   enviarMail("leochabur@gmail.com", $mail, "Nueva Orden de Turismo Generada");

                 //enviarMail("leochabur@gmail.com,mdepeon@masterbus.net,raguiar@masterbus.net,turismo@masterbus.net,rpizzutti@masterbus.net", $mail, "Nueva Orden de Turismo Generada");
                 
              }
              commit($conn);
              cerrarconexcion($conn);
              $response[msge] = "Se ha generado con exito la orden!";
              print (json_encode($response));
              }catch (Exception $e) {
                                      rollback($conn);
                                      cerrarconexcion($conn);
                                      $response[status] = false;
                                      $response[msge]=$e->getMessage()." toronjaaaaaaaaaaaaaaaaaaaaaa";
                                      print (json_encode($response));
                                      };
              
  }elseif ($accion == 'mortur'){ //modifica una orden cargada desde turismo
     $informar = true;
     $estructura = STRUCTURED;
     $price_final = $_POST['preciofinal'];
     $viaticos_t = $_POST['viaticos'];

     if(!$_POST[realizada]){ //si el servicio aun no se ha llevado a cabo modifica, tanto la orden como la orden de turismo
     
              $conn = conexcion();
              $fecha_ida =  dateToMysql($_POST['fsalida'],'/');
              $hora_salida_ida = $_POST['hsalida'];
              $hora_llegada_ida = $_POST['hllegada'];

              $fecha_regreso =  dateToMysql($_POST['fregreso'],'/');
              $hora_salida_regreso = $_POST['hsalidaregreso'];
              $hora_llegada_regreso = $_POST['hllegadaregreso'];
              
              $salida_servicio = new DateTime("$fecha_ida");

              if (! isset($_POST['srg'])){
                 $fecha_regreso =  dateToMysql($_POST['fregreso'],'/');
                 $hora_salida_regreso = $_POST['hsalidaregreso'];
                 $hora_llegada_regreso = $_POST['hllegadaregreso'];
                 $regreso_servicio = new DateTime("$fecha_regreso");
              }


              
              $campos = "id_estructura, id_claseservicio, id_estructuraclaseservicio, id_user, id_cliente, id_estructura_cliente, nombre,
                         id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino,
                         fservicio, hcitacion, hsalida, hllegada, hfinservicio, km";

              $values="$estructura, $_POST[clase], $estructura";


              if (! isset($_POST['srg'])){
                     $interval = $salida_servicio->diff($regreso_servicio);
                     $dias = $interval->format('%d');
              }
              else{
                   $dias = 0;
              }
              /////////////////////////////////////////////////////////


              $response = array();     ///respuesta al cliente de la accion requerida
              $response[status] = true;
              $mail="";
              try{

                  begin($conn);
                  //verifica si se han cambiado alguna de las fechas, en este caso elimina todas las ordenes y vuelve a generar los servicios
                  $cambio = cambioFecha($_POST[orden], $fecha_ida, (isset($_POST['srg'])?'NULL':"'$fecha_regreso'"), $conn);

                  if ($cambio){     ///alguna de las fechas ha cambiado salida o regreso
                     //die(cambio);
                     $mail = generarMailDatosOrden($_POST[orden], "DATOS ORIGINALES DE LA ORDEN", $conn);
                     
                     //obtenemos la orden ; si existe; de regreso, para que sea modificada o creada
                     $orden_reg = getOrdenRegresoAsociada($_POST[orden], $conn);
                     
                     ///elimina todas las ordenes de asociadas con la orden original , el proximo paso es reactivar si es que existiera la orden de regreso
                     ///obtenida en la linea anterior
                     eliminarOrdenesAsociadasTurismo($_POST[orden], $conn);

                    /* if ($orden_reg){
                        update('ordenes', "borrada", "0", "(id = $row[0])", $conn);
                     }     */
                     
                     if ($dias == 0){ //el servicio sale y regresa el mismo dia
                          // no es necesario reactivar -si existe- la orden de regreso asociada

                          $km = ($_POST['km']*2);

                          if (isset($_POST['srg'])){  //solo es un servicio de ida o solo de regreso, pero un solo tramo
                             $km = $_POST['km'];
                             $hllegada = $hora_llegada_ida;
                             $hfins = $hora_llegada_ida;
                          }
                          else{
                               $hllegada = $hora_llegada_regreso;
                               $hfins = $hora_llegada_regreso;

                          }
                          $values.= ", $_SESSION[userid], $_POST[cliente], $estructura, '$_POST[nombre]', $_POST[origen], $estructura, $_POST[destino], $estructura, '$fecha_ida', '$hora_salida_ida', '$hora_salida_ida', '$hllegada', '$hfins', $km";
                          backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                          update('ordenes', $campos, $values, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                     }
                     elseif($dias > 0){ ///el servicio sale un dia y regresa no el mismo dia
                             //datos para actualizar la orden de ida del servicio

                             $km = $_POST['km'];
                             $valores = $values.", $_SESSION[userid], $_POST[cliente], $estructura, '$_POST[nombre] (IDA)', $_POST[origen], $estructura, $_POST[destino], $estructura,
                                        '$fecha_ida', '$hora_salida_ida', '$hora_salida_ida', '$hora_llegada_ida', '$hora_llegada_ida', $km";
                             backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                             update('ordenes', $campos, $valores, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                             ///finaliza actualizacion orden de ida
                           //  $response[msge]="entrara en viaje";
                           //  die (json_encode($response));
                             //para el caso que el servicio permanezca mas de un dia en destino crea las ordnees EN VIAJE correspondientes
                             for ($i=1; $i < $dias; $i++){
                                 $salida_servicio->add(new DateInterval('P1D'));
                                 $valores = $values.", $_SESSION[userid], $_POST[cliente], $estructura, 'EN VIAJE', $_POST[destino], $estructura ,$_POST[destino], $estructura, '".$salida_servicio->format('Y-m-d')."', '00:00', '00:00', '23:59', '23:59', 0";

                                 $response[msge] = "Se ha generado un error al intentar guardar la orden en viaje del dia $i!!!";
                                 $orden_anexas = insert('ordenes', "id, $campos", $valores, $conn);
                                 ///asocia cada orden a la orden de turismo primera, para que se pueda acceder a que servicios armo la orden de turismo
                                 insert('ordenes_asocioadas', "id, id_orden, id_estructura_orden, id_orden_asociada, id_esructura_orden_asociada", "$_POST[orden], $estructura, $orden_anexas, $estructura", $conn);
                             }

                             
                             $valores = $values.", $_SESSION[userid], $_POST[cliente], $estructura, '$_POST[nombre] (REGRESO)', $_POST[destino], $estructura ,$_POST[origen], $estructura, '$fecha_regreso', '$hora_salida_regreso', '$hora_salida_regreso', '$hora_llegada_regreso', '$hora_llegada_regreso', $km";
                            // $response[msge]="no dentro no dentor";
                          //   die (json_encode($response));
                             if ($orden_reg){
                                backup('ordenes', 'ordenes_modificadas', "(id = $orden_reg) and (id_estructura = ".STRUCTURED.")", $conn);
                                update('ordenes', "$campos, borrada", "$valores, 0", "(id = $orden_reg) and (id_estructura = ".STRUCTURED.")", $conn);
                             }
                             else{
                               //   die(json_encode("gran tiro"));
                                  $orden_reg = insert("ordenes", "id, $campos", $valores, $conn);
                             }
                             procesarOrdenTurismo($orden_reg, $fecha_regreso, 0, $_POST, $conn);
                     }
                     $ord_tur = procesarOrdenTurismo($_POST[orden], $fecha_regreso, 1, $_POST, $conn);
                  }
                  else{//las fechas originales no han cambiado

                       $mail = generarMailDatosOrden($_POST[orden], "DATOS ORIGINALES DE LA ORDEN", $conn);//genera el mail con los datos de la orden original

                       $orden_reg = getOrdenRegresoAsociada($_POST[orden], $conn);

                       if ($dias == 0){ //la orden se realiza el mismo dia

                          $km = ($_POST['km']*2);
                          if (isset($_POST['srg'])){  //solo es un servicio de ida o solo de regreso, pero un solo tramo
                             $km = $_POST['km'];
                             $hllegada = $_POST['hllegada'];
                             $hfins = $_POST['hllegada'];
                          }
                          else{
                               $hllegada = $_POST['hllegadaregreso'];
                               $hfins = $_POST['hllegadaregreso'];

                          }

                          $valores = $values.", $_SESSION[userid], $_POST[cliente], $estructura, '$_POST[nombre]', $_POST[origen], $estructura ,$_POST[destino], $estructura, '$fecha_ida', '$hora_salida_ida', '$hora_salida_ida', '$hllegada', '$hfins', $km";

                          backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                          update('ordenes', $campos, $valores, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                       }
                       elseif($dias > 0){ //el servicio sale un dia y regresa al otro /debe actualizar a orden de ida, de vuelta mas la orden de turismo
                             $km = $_POST['km'];
                             //datos para actualizar la orden de ida del servicio
                             $valores = $values.", $_SESSION[userid], $_POST[cliente], $estructura, '$_POST[nombre](IDA)', $_POST[origen], $estructura ,$_POST[destino], $estructura, '$fecha_ida', '$hora_salida_ida', '$hora_salida_ida', '$hora_llegada_ida', '$hora_llegada_ida', $km";
                             backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                             update('ordenes', $campos, $valores, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                             ///finaliza actualizacion orden de ida
                             
                             
                             //actuliza orden de regreso si existe sino la crea
                             $valores = $values.", $_SESSION[userid], $_POST[cliente], $estructura, '$_POST[nombre](REGRESO)', $_POST[destino], $estructura ,$_POST[origen], $estructura, '$fecha_ida', '$hora_salida_regreso', '$hora_salida_regreso', '$hora_llegada_regreso', '$hora_llegada_regreso', $km";
                             if ($orden_reg){
                                backup('ordenes', 'ordenes_modificadas', "(id = $orden_reg) and (id_estructura = ".STRUCTURED.")", $conn);
                                update('ordenes', $campos, $valores, "(id = $orden_reg) and (id_estructura = ".STRUCTURED.")", $conn);
                             }
                             else{
                                  $orden_reg =  insert("ordenes", "id, $campos", $valores, $conn);
                             }
                             procesarOrdenTurismo($orden_reg, $fecha_regreso, 0, $_POST, $conn);
                       }
                       $ord_tur = procesarOrdenTurismo($_POST[orden], $fecha_regreso, 1, $_POST, $conn);

                  }

              
              
              
              



              /*    backup('ordenes', 'ordenes_modificadas', "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);
                  update('ordenes', $campos, $values, "(id = $_POST[orden]) and (id_estructura = ".STRUCTURED.")", $conn);

                  */

                  delete("gastos_por_servicio_turismo", "id_orden, id_estructura_orden", "$_POST[orden], $estructura", $conn); //elimina todos los gatos asociados para volver a dar de alta los modificados
                  $sql = "SELECT id FROM items_gastos_turismo i order by detalle";
                  $result = ejecutarSQL($sql, $conn);
                  while ($row = mysql_fetch_array($result)){
                        if (isset($_POST["gas$row[id]"])){
                           insert("gastos_por_servicio_turismo", "id, id_item_gasto, id_orden, id_estructura_orden", "$row[0], $_POST[orden], $estructura", $conn);
                        }
                  }

                  $precio = $_POST['preciofinal'] == ""?'NULL':$_POST['preciofinal']; //define que hara en la cta cte $0.0 cortesia
                  $campos_ctacte = "id_cliente, id_estructura_cliente, importe, viaje_pago, fecha_ingreso, id_user";
                  $values_ctacte = "$_POST[cliente], $estructura, $precio, 'v', now(), $_SESSION[userid]";
                  $result = ejecutarSQL("SELECT id FROM ctacteturismo WHERE (id_orden = $ord_tur)", $conn);
                  if ($data = mysql_fetch_array($result)){
                     if ($precio == 'NULL'){
                        delete("ctacteturismo", "id_orden", "$ord_tur", $conn);
                     }
                     else{
                          update('ctacteturismo', $campos_ctacte, $values_ctacte, "(id = $data[0])", $conn);
                     }
                  }
                  else{
                       if ($precio != 'NULL'){
                          insert('ctacteturismo', "id, id_orden, id_estructura_orden,".$campos_ctacte, "$ord_tur, $estructura, $values_ctacte", $conn);
                       }
                  }
                  commit($conn);
                  
                  ///una vez que actualizo las ordenes sin errores, anexa al mail, los nuevos datos de la orden
           //       $mail.= "<br><br>".generarMailDatosOrden($_POST[orden], "NUEVOS DATOS DE LA ORDEN", $conn);

                 // enviarMail("leochabur@gmail.com,mdepeon@masterbus.net,raguiar@masterbus.net,turismo@masterbus.net,rpizzutti@masterbus.net", $mail, "ORDEN DE TURISMO MODIFICADA");
              //    enviarMail("leochabur@gmail.com", $mail, "ORDEN DE TURISMO MODIFICADA");
                  $response[msge] = "Se ha actualizado con exito la orden!";
                  cerrarconexcion($conn);
                  print (json_encode($response));
              }catch (Exception $e) {
                                      $response[status] = false;
                                      $response[msge]="No se ha podido realizar la modificacion solicitada!!!";
                                      $response[sql]=$e->getMessage();
                                      rollback($conn);
                                      cerrarconexcion($conn);
                                      print (json_encode($response));
                                      };
      }
      else{ //si el servicio ya se realizo solo modifica valores de venta del viaje y viaticos de conductor

              
              $conn = conexcion();
              $response = array();     ///respuesta al cliente de la accion requerida
              $response[status] = true;
              try{
                       begin($conn);
                       $campos ="precio_venta_final, viaticos, afecta_ctacte, efc, id_estructura_orden";
                       $precio_venta = $price_final==''?'NULL':$price_final;
                       $values = "$precio_venta, ".($viaticos_t?$viaticos_t:'NULL').", 1, ".(isset($_POST['efc'])?1:0).", $estructura";
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
                       $result = ejecutarSQL("SELECT id FROM ctacteturismo WHERE (id_orden = $orden_mod)", $conn);
                       if ($data = mysql_fetch_array($result)){
                          update('ctacteturismo', $campos_ctacte, $values_ctacte, "(id = $data[0])", $conn);
                       }
                       else{
                            insert('ctacteturismo', "id, id_orden, id_estructura_orden,".$campos_ctacte, "$orden_mod, $estructura, $values_ctacte", $conn);
                       }
              
              
              
                       commit($conn);
                       cerrarconexcion($conn);
                       $response[msge] = "Se ha generado con exito la orden!";
                       print (json_encode($response));
              }catch (Exception $e) {
                                      rollback($conn);
                                      cerrarconexcion($conn);
                                      $response[status] = false;
                                      $response[msge]="$campos - $values - No se ha podido realizar la modificacion solicitada!aa!!";
                                      $response[sql]=$e->getMessage();
                                      print (json_encode($response));
                                      };
      }
  }
  
  function eliminarOrdenesAsociadasTurismo($orden, $conn){
           $sql = "SELECT id FROM ordenes where id in (select id_orden_asociada from ordenes_asocioadas where id_orden = $orden)";
           $result = mysql_query($sql, $conn);
           while ($row = mysql_fetch_array($result)){
                 update('ordenes', "borrada", "1", "(id = $row[0])", $conn);
           }
           $sql = "update ordenes set borrada = 1, id_user = $_SESSION[userid], fecha_accion = now() where id in (select id_orden_asociada from ordenes_asocioadas where id_orden = $orden)";
           mysql_query($sql, $conn);
  }
  
  function cambioFecha($orden, $fechaSalida, $fechaRegreso, $conn){ //verifica si la fecha de la orden ha cambiado, de ser asi da de baja todos el conjunto de orden
           $sql = "select * from ordenes o left join ordenes_turismo ot on ot.id_orden = o.id and ot.id_estructura_orden = o.id_estructura where (o.id = $orden ) and (('$fechaSalida' <> o.fservicio) or if (fecha_regreso is null,if ($fechaRegreso is null,0, 1),if ($fechaRegreso is null, 1,if (fecha_regreso = $fechaRegreso, 0, 1))))";
           $result = ejecutarSQL($sql, $conn);
           return mysql_num_rows($result);
  }
  
  function eliminarOrdenesTurismo($orden, $conn){
           try{
              $campos = "borrada, id_user, fecha_accion";
              $values = "1, $_SESSION[userid], now()";
              backup('ordenes', 'ordenes_modificadas', "(id = $orden) and (id_estructura = ".STRUCTURED.")", $conn);
              update('ordenes', $campos, $values, "(id = $orden) and (id_estructura = ".STRUCTURED.")", $conn);
              $sql="SELECT id_orden_asociada, id_esructura_orden_asociada
                    FROM ordenes_asocioadas oa
                    inner join ordenes o on o.id = oa.id_orden and o.id_estructura = oa.id_estructura_orden
                    where oa.id_orden = $orden";
              $result = ejecutarSQL($sql, $conn);
              while($row = mysql_fetch_array($result)){
                         backup('ordenes', 'ordenes_modificadas', "(id = $row[0]) and (id_estructura = $row[1])", $conn);
                         update('ordenes', $campos, $values, "(id = $row[0]) and (id_estructura = $row[1])", $conn);
              
              }
           }catch (Exception $e) {}
  }
  
  function generarMailDatosOrden($orden, $title, $conn){  //genera datos de orden para el correo
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
                          where o.id = $orden";
                  $result = ejecutarSQL($sql, $conn);
                  if ($data = mysql_fetch_array($result)){
                           $mail = "<b><i><u>$title</u></i></b><br>
                                     Cliente: $data[0]
                                     Fecha: $data[1]
                                     Hora Salida: $data[2]<br>
                                     Capacidad Solicitada: $data[3]<br>
                                     Origen: $data[4]  Destino:$data[5]<br>
                                     Contacto: $data[6]
                                     Tel. Contacto: $data[7]<br>
                                     Fecha Regreso: $data[8] Hora Regreso: $data[9]";
                  }
                  return $mail;
  }
  
  function getOrdenRegresoAsociada($orden, $conn){
           $sql="SELECT id_orden_asociada
                 FROM ordenes o
                 inner join ordenes_asocioadas oa on oa.id_orden = o.id and oa.id_estructura_orden = o.id_estructura
                 where id_orden = $orden
                 order by fservicio";
           $result = ejecutarSQL($sql, $conn);
           $num_ord = 0;
           while ($row = mysql_fetch_array($result)){
              $num_ord = $row[0];
           }
           return $num_ord;
  }
  
  function procesarOrdenTurismo($orden, $fecharegot, $afecta_ctacte, $val, $conn){
       try{
           $campos = "precio_venta_final, viaticos, id_estructura_orden, contacto, tel_contacto, mail_contacto, lugar_salida, lugar_llegada, capacidad_solicitada, hora_regreso, fecha_regreso, afecta_ctacte, efc, bar, banio, tv, mantas, microfono, mov_dest, observaciones"; //, id_user, fecha_accion";
           $estructura = STRUCTURED;
           $precio = $val['preciofinal'] == ""?'NULL':$val['preciofinal'];
           $viatico = $_POST[viaticos]==''?'NULL':$_POST[viaticos];
           $contacto =  cambiarStr($_POST[nomcontacto]);
           $tel_con = cambiarStr($_POST[telcontacto]);
           $mail = cambiarStr($_POST[mailcontacto]);
           $salida = cambiarStr($_POST[lugarsalida]);
           $llegada = cambiarStr($_POST[lugarllegada]);
           $values = "$precio, $viatico, $estructura, '$contacto', '$tel_con', '$mail', '$salida', '$llegada', $_POST[pax], '$_POST[hsalidaregreso]', '$fecharegot', $afecta_ctacte, ".(isset($_POST[efc])?1:0).",".(isset($_POST[bar])?1:0).', '.(isset($_POST[banio])?1:0).','.(isset($_POST[dvd])?1:0).",".(isset($_POST[mantas])?1:0).','.(isset($_POST[mic])?1:0).','.(isset($_POST[excur])?1:0).",'".cambiarStr($_POST[observa])."'";//, $_SESSION[userid], now()";
                       
           $result = ejecutarSQL("SELECT id FROM ordenes_turismo WHERE id_orden = $orden",$conn);
           if ($row = mysql_fetch_array($result)){
              backup('ordenes_turismo', 'ordenes_turismo_modificadas', "(id = $row[0])", $conn);
              update('ordenes_turismo', $campos, $values, "(id = $row[0])", $conn);
              return $row[0];
           }
           else{
                return insert('ordenes_turismo', "id, id_orden, $campos", "$orden, $values", $conn);
           }
       }catch (Exception $e) {
                             throw $e;
                             };
  }
  
  function cambiarStr($str){
           return str_replace(",",";",$str);
  }
  
  function getPagosTurismo($orden_tur, $conn){
           $sql = "SELECT * FROM pagosturismo p where id_orden_turismo = $orden_tur";
           $result = ejecutarSQL($conn);
           $pagos = array();
           while ($row = mysql_fetch_array($result)){
                 $pagos[] = $row;
           }
           return $pagos;
  }
  
  function existeOrdenTurismo($orden, $conn){
           $sql = "SELECT * FROM ordenes_turismo WHERE id_orden = $orden";
           $result = ejecutarSQL($sql, $conn);
           return mysql_num_rows($result);
  }
  
                  /* /////



                  //die("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)");
                  $result = ejecutarSQL("SELECT id FROM ordenes_turismo WHERE (id_orden = $_POST[orden]) and (".STRUCTURED." = id_estructura_orden)", $conn);

                  if ($data = mysql_fetch_array($result)){ //se ha agregado el registro a la tabla ordenes_turismo
                     $orden_mod=$data[0];
                     update('ordenes_turismo', $campos, $values, "(id = $data[0])", $conn);
                  }
                  else{
                       $orden_mod=insert('ordenes_turismo', "id, id_orden, $campos", "$_POST[orden], $values", $conn);
                  }
  }
  
  /*
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
  */
  
?>
