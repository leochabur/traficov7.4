<?
  session_start();

  include_once('../../controlador/ejecutar_sql.php');
  include_once('../../controlador/bdadmin.php');
  include_once('../../modelo/enviomail/sendmail.php');
  include_once('../../modelo/enviomail/sendordbjafn.php');
  define(STRUCTURED, $_SESSION['structure']);
  $accion= $_POST['accion'];
  
  if ($accion == 'cls'){
     $id = $_POST['orden']; // id del registro
   //if ($_SESSION['structure'] == 1){
   if(false){
     $conn = conexcion();
     $sql = "select o.id_cliente, id_micro, upper(nombre) as nombre, date_format(fservicio, '%d/%m/%Y') as fecha, upper(razon_social) as cliente, filtralistaunidades as flista
             from ordenes o
             inner join clientes c on o.id_cliente = c.id and c.id_estructura = o.id_estructura_cliente
             inner join restricciongralcliente rgc on rgc.id_cliente = c.id and rgc.id_estructuraCliente = c.id_estructura
             where o.id = $id";
     $result = mysql_query($sql, $conn); // obtiene interno y cliente de la orden
     if ($row = mysql_fetch_array($result)){
      if($row['id_cliente'] != 13){
        $nombre=$row['nombre'];
        $fecha=$row['fecha'];
        $cliente=$row['cliente'];
        if ($row[flista]){
           $filtraunidades="SELECT u.id as id_coche, interno, id_tipounidad, id_estructura_tipounidad, r.id_cliente, r.id_estructuracliente
                            FROM restcochesclientes r
                            inner join (select * from unidades where id_estructura = $_SESSION[structure] and id = $row[id_micro]) u on u.id = r.id_coche
                            left join restricciongralcliente rgc on rgc.id_cliente = r.id_cliente and rgc.id_estructuraCliente = r.id_estructuracliente
                            where permitido and r.id_cliente = $row[id_cliente] and (if (ant_max is null, true ,(year(now()) - anio) <= ant_max)) and activo ";
        }
        else{
             $filtraunidades = "SELECT u.id as id_coche, interno, id_tipounidad, id_estructura_tipounidad FROM unidades";
        }
        
        
        $sql = "select id_coche, interno, 1 as disponible
                from (
                      $filtraunidades
                     ) o
                inner join (select * from restclientetipounidad where id_tipovto is null) rctu on rctu.id_cliente = o.id_cliente and
                                                                                               o.id_estructuracliente = rctu.id_estructuracliente and
                                                                                               rctu.id_tipounidad = o.id_tipounidad and
                                                                                               rctu.id_estructura_tipounidad = o.id_estructura_tipounidad";
        $result = mysql_query($sql, $conn);
        if (!mysql_num_rows($result)){
              $sql = "select if (id in (SELECT id_coche FROM restcochesclientes where id_coche = $row[id_micro] and id_cliente = $row[id_cliente] and permitido),1,0) as incluye,
                             if ((year(now()) - anio) <= (select ant_max from restricciongralcliente where id_cliente = $row[id_cliente]), 1, 0) as ant,
                             if (id_tipounidad in (SELECT id_tipounidad FROM restclientetipounidad where id_cliente = $row[id_cliente] and id_tipounidad is not null), 1, 0) as tipo,
                             interno
                     from unidades u
                     where id = $row[id_micro]";
              $result = mysql_query($sql, $conn);
              if ($row = mysql_fetch_array($result)){
                 $cuerpo = "El interno $row[interno], que realizo el servicio $nombre, el dia $fecha, no se ajusta a los parametros solicitados por el cliente $cliente, debido a:";
                 $cuerpo.= (!$row['incluye']?"<br>No se encuentra dentro de la lista de coches asignados al cliente":"");
                 $cuerpo.= (!$row['ant']?"<br>No se encuentra dentro de la antiguedad maxima solictada por el cliente":"");
                 $cuerpo.= (!$row['tipo']?"<br>El tipo de unidad no es admitido por el cliente":"");
           //      enviarMail("dscirocco@masterbus.net,leochabur@gmail.com,mdepeon@masterbus.net,rpizzutti@masterbus.net,raguiar@masterbus.net,ealmada@masterbus.net,ddanchuk@masterbus.net" , $cuerpo, "Conflictos con la orden $id");
               //  enviarMail("leochabur@gmail.com" , $cuerpo, "Conflictosssssssssssssssssssssssssssss con la orden $id");
              }
        }
        }
     }
    }
  //   enviarMail("leochabur@gmail.com", "$sql", "Ordenes Desactivadas nchk");

     $campo = 'id_user, fecha_accion, finalizada';
     $value = "$_SESSION[userid], now(), 1";
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
     
  }
elseif ($accion == 'susp'){
     $id = $_POST['orden']; // id del registro
     $campo = 'id_user, fecha_accion, suspendida';
     $value = "$_SESSION[userid], now(), 1";
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     $res = update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");
     if ($res)
        sentMail($id);
     print $res;
  }
elseif ($accion == 'dupl'){
     $id  = $_POST['orden'];
     $sql = "SELECT id, id_estructura, fservicio,    nombre,   hcitacion, hsalida,   hllegada,  hfinservicio, km,     id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, finalizada, borrada, comentario, vacio,      id_user,          fecha_accion, cantpax, suspendida, checkeada FROM ordenes where id = $id";
     $result = ejecutarSQL($sql);
     if ($row = mysql_fetch_array($result)){
        $clivac='null';
        $eclivac='null';
        if ($row['id_cliente_vacio']){
           $clivac= $row['id_cliente_vacio'];
           $eclivac= $row['id_estructura_cliente_vacio'];
        }
        $srv='null';
        $srvst='null';
        
        if ($row['id_servicio']){
           $srv= $row['id_servicio'];
           $srvst= $row['id_estructura_servicio'];
        }
        $campos = "id, id_estructura, fservicio,    nombre,   hcitacion, hsalida,   hllegada,  hfinservicio, km,     id_servicio, id_estructura_servicio, id_ciudad_origen, id_estructura_ciudad_origen, id_ciudad_destino, id_estructura_ciudad_destino, id_cliente, id_estructura_cliente, id_cliente_vacio, id_estructura_cliente_vacio, finalizada, borrada, comentario, vacio,      id_user,          fecha_accion, cantpax, suspendida, checkeada";
        $values = "     $row[1],    '$row[2]', '$row[3]', '$row[4]', '$row[5]','$row[6]',   '$row[6]',      $row[8], $srv,      $srvst,                $row[11],           $row[12],                    $row[13],           $row[14],                   $row[15],      $row[16],              $clivac,          $eclivac,              0,          0,      ''         ,$row[22],    $_SESSION[userid], now(),       0,      0,0";
        print insert('ordenes', $campos, $values);
     }

  }
elseif ($accion == 'check'){
     $id = $_POST['orden']; // id del registro
     $campo = 'id_user, fecha_accion, checkeada';
     $value = "$_SESSION[userid], now(), 1";
     backup('ordenes', 'ordenes_modificadas', "(id = $id) and (id_estructura = ".STRUCTURED.")");
     print update('ordenes', $campo, $value, "(id = $id) and (id_estructura = ".STRUCTURED.")");

  }elseif($accion == 'ldo'){
                  $conn = conexcion();
                  $id = $_POST['orden']; // id del registro
                  $sql="SELECT vacio, date_format(hcitacion, '%H:%i') as hcitacion, date_format(hsalida, '%H:%i') as hsalida, date_format(hllegada, '%H:%i') as hllegada, date_format(hfinservicio, '%H:%i') as hfinservicio, id_ciudad_origen, id_ciudad_destino, id_cliente, id_chofer_1, if (ISNULL(id_cliente_vacio), 0, id_cliente_vacio) as id_cliente_vacio, km, fservicio
                        FROM ordenes o
                        WHERE (id = $id)";
                  $result = mysql_query($sql, $conn) or die(mysql_error($conn));
                  $rta = '';
                  if (mysql_num_rows($result)){
                     $data = mysql_fetch_array($result);
                     $rta = $data['hcitacion'];
                     $rta.= '-'.$data['hsalida'];
                     $rta.= '-'.$data['hllegada'];
                     $rta.= '-'.$data['hfinservicio'];
                     $rta.= '-'.$data['id_unidad_interno'];
                     $rta.= '-'.$data['id_ciudad_origen'];
                     $rta.= '-'.$data['id_ciudad_destino'];
                     $rta.= '-'.$data['id_cliente'];
                     $rta.= '-'.$data['id_chofer_1'];
                     $rta.= '-'.$data['vacio'];
                     $rta.= '-'.$data['km'];
                     $rta.= '-'.$data['fservicio'];
                  }
                  mysql_close($conn);
                  print $rta;
  }elseif($accion == 'update'){
                  $campo = "id_user, fecha_accion,".$_POST['cmp'];
                  $value = $_POST['valor'];
                  $struct = STRUCTURED;
                  if($_POST['valor'] == 0){
                     $value = 'NULL';
                     $struct = 'NULL';
                  }
                  $valor = "$_SESSION[userid], now(), $value";
                  $clave = $_POST['clave'];

                  if (isset($_POST['cmpstruct'])){
                     /*if ($_POST['cmpstruct'] == 'iem'){
                        $campo.=', id_estructura_micro';
                        $valor.=', '.$struct;
                     }
                     else*/
                     if ($_POST['cmpstruct'] == 'iech1'){
                            $campo.=', id_estructura_chofer1';
                            $valor.=', '.$struct;
                     }
                     elseif ($_POST['cmpstruct'] == 'iech2'){
                            $campo.=', id_estructura_chofer2';
                            $valor.=', '.$struct;
                     }
                  }
                  backup('ordenes', 'ordenes_modificadas', "(id = $clave) and (id_estructura = ".STRUCTURED.")");
                  print update('ordenes', $campo, $valor, "(id = $clave) and (id_estructura = ".STRUCTURED.")");
  }
?>

