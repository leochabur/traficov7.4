<HTML>
<HEAD>
 <TITLE>New Document</TITLE>
</HEAD>
<BODY>
<?
                    if (isset($_POST[regdif])){ // tiene que generar el vacio de vuelta cuando lo lleva y el de ida cuando lo va a buscar
                     $sql = "SELECT valor FROM opciones where id_estructura = $estructura and opcion = 'cliente-vacio'";
                     $result = ejecutarSQL($sql, $conn);
                     if ($row = mysql_fetch_array($result)){
                        $cli_vac = $row[0];
                     }
                     $horasal=$_POST[hsalida];
                     $horalleg=$_POST[hllegada];
                     $dif=date("H:i:s", strtotime("00:00:00") + strtotime($horalleg) - strtotime($horasal) );
                     $hm = explode(':', $dif);

                     $dtsalida = new DateTime("$fecha $horasal");
                     $dtsalida->add(new DateInterval("PT$hm[0]H$hm[1]M"));

                     $campos="id, id_estructura, fservicio, nombre, hcitacion, hsalida, hllegada, hfinservicio, km,
                              id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino,
                              id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio,
                              finalizada, borrada, comentario, vacio, id_user, fecha_accion, suspendida, checkeada, id_claseservicio,
                              id_estructuraclaseservicio";
                     $fecha_regreso_vacio = $dtsalida->format('Y-m-d');
                     $hora_salida_regreso_vacio = $dtsalida->format('H:i:s');
                     $dtsalida->add(new DateInterval("PT$hm[0]H$hm[1]M"));
                     $hora_llegada_regreso_vacio = $dtsalida->format('H:i:s');
                     $values="$estructura, '$fecha_regreso_vacio', '(VACIO REGRESO) - $_POST[nombre]', '$hora_salida_regreso_vacio',
                              '$hora_salida_regreso_vacio', '$hora_llegada_regreso_vacio', '$hora_llegada_regreso_vacio',
                               $km, $destino, $estructura, $origen, $estructura, $cli_vac, $estructura, $cliente, $estructura,
                               0,0,'Corresponde a Vacio de Orden $orden', 1, $_SESSION[userid], now(), 0,0, $_POST[clase], $estructura";
                     $vacio_vta = insert('ordenes', $campos, $values, $conn);  //genera el vacio de regreso  al llevarlos


                     /////arma el dia y horario del regreso diferido ///////////////
                     $fecha = $_POST['fregreso'];
                     $fecha = explode("/", $fecha);
                     $fecha = $fecha[2].'-'.$fecha[1].'-'.$fecha[0];
                     $horasal=$_POST[hsalidaregreso];
                     $dtsalida = new DateTime("$fecha $horasal");
                     $dtsalida->sub(new DateInterval("PT$hm[0]H$hm[1]M")); //calcula a que hora debe salir el vacio para realizar el regreso del viaje
                     $fecha_ida_vacio = $dtsalida->format('Y-m-d'); //fecha en la que debe salir el vacio para realizar el regreso
                     $hora_salida_ida_vacio = $dtsalida->format('H:i:s');//hora en la que debe salir el vacio para realizar el regreso
                     $values="$estructura, '$fecha_ida_vacio', '(VACIO IDA) - $_POST[nombre]', '$hora_salida_ida_vacio',
                              '$hora_salida_ida_vacio', '$_POST[hsalidaregreso]', '$_POST[hsalidaregreso]',
                               $km, $origen, $estructura, $destino, $estructura, $cli_vac, $estructura, $cliente, $estructura,
                               0,0,'Corresponde a Vacio de Orden $orden', 1, $_SESSION[userid], now(), 0,0, $_POST[clase], $estructura";
                     $vacio_ida = insert('ordenes', $campos, $values, $conn); //genera el vacio de ida a buscarlos

                     //genera el servicio de regreso con el contingente ya a bordo
                     $values="$estructura, '$fecha', '$_POST[nombre] - VUELTA', '$_POST[hsalidaregreso]',
                              '$_POST[hsalidaregreso]', '$_POST[hllegadaregreso]', '$_POST[hllegadaregreso]',
                               $km, $destino, $estructura, $origen, $estructura, $cliente, $estructura, null, null,
                               0,0,'Servicio Vacio de Orden $orden', 0, $_SESSION[userid], now(), 0,0, $_POST[clase], $estructura";
                     $orden_ida_a_buscar = insert('ordenes', $campos, $values, $conn);

                     $campos = "id, id_orden, precio_venta_neto, precio_venta_final, id_estructura_orden,
                                fecha_regreso, capacidad_solicitada, hora_regreso, observaciones";
                     $values = "$orden_ida_a_buscar, 0, 0, $estructura, '$fecha', $_POST[pax], '$_POST[hsalidaregreso]', 'Corresponde a servicio de vuelta cuya ida se realizo el dia $fecha_salida_original'";
                     insert('ordenes_turismo', $campos, $values, $conn);
                     //die ("diferencia ".$dtsalida->format('Y-m-d H:i'));
                  }
?>
</BODY>
</HTML>
